import paho.mqtt.client as mqtt
import json
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
from matplotlib.animation import FuncAnimation
from collections import deque
from datetime import datetime
import threading
import os
import time

auth_username = "admin"
auth_password = "Admintest1"
broker_url = "04dff8ffebe94e9d9bcee24fb6e655ed.s1.eu.hivemq.cloud"

live_plotter = None

def create_client():
    client = mqtt.Client(transport='tcp',
                        protocol=mqtt.MQTTv5)
    client.tls_set(tls_version=mqtt.ssl.PROTOCOL_TLS)
    client.username_pw_set(auth_username, auth_password)
    port_tcp = 8883
    client.connect(broker_url,
                    port=port_tcp,
                    clean_start=mqtt.MQTT_CLEAN_START_FIRST_ONLY,
                    keepalive=60);
    return client

def report_connect_status(client, userdata, flags, rc, properties=None):
    print("trying to connect")
    if rc == 0:
        print("Connected to MQTT Broker!")
    else:
        print("Failed to connect with error code %d\n", rc)

# alternative callback that returns the success code when connecting
def on_connect(client, userdata, flags, rc, properties=None):
    print("CONNACK received with code %s." % rc)

# with this callback you can see if your publish was successful
def on_publish(client, userdata, mid, properties=None):
    print("mid: " + str(mid))

# print which topic was subscribed to
def on_subscribe(client, userdata, message_id, granted_qos, properties=None):
    print("Subscribed: " + str(message_id) + " " + str(granted_qos))

# print message, useful for checking if it was successful
def on_message(client, userdata, msg):
    global live_plotter

    print(msg.topic + " " + str(msg.qos))
    data = json.loads(msg.payload)

    print("\nPrestatie:")
    print("-" * 40)

    units = {
        "hartslag": "bpm",
        "systolische_bloeddruk": "mmHg",
        "lactaat_waardes": "mmol/L",
        "zuurstof_opname": "mL/kg/min",
        "hartminuutvolume": "L/min",
        "maximale_belasting": "W",
        "anaerobe_drempel": "bpm"
    }

    # Zorgen voor mooiere print in de terminal
    for key, value in data.items():
        unit = units.get(key, "")
        clean_key = key.replace("_", " ").title()
        if unit:
            print(f" - {clean_key}: {value} {unit}")
        else:
            print(f" - {clean_key}: {value}")
    print("-" * 40 + "\n")

    # Nodig voor de grafiek
    if live_plotter:
        live_plotter.add_data(data)

class LivePlotter:
    def __init__(self, max_points):
        # Om te stoppen op 90 (aangezien een match 90 minuten duurt)
        self.max_points = max_points
        self.data = {
            "hartslag": deque(maxlen=max_points),
            "systolische_bloeddruk": deque(maxlen=max_points),
            "lactaat_waardes": deque(maxlen=max_points),
            "zuurstof_opname": deque(maxlen=max_points)
        }
        # Eenheden toevoegen
        self.units = {
            "hartslag": "bpm",
            "systolische_bloeddruk": "mmHg",
            "lactaat_waardes": "mmol/L",
            "zuurstof_opname": "mL/kg/min",
        }
        self.minutes = deque(maxlen=max_points)
        self._minute_counter = 1
        self.minute_limit = max_points
        self.timestamps = deque(maxlen=max_points)
        self.client = None
        self._stopped = False

        # Nodig om de mak voor de live_grafiek.png aan te maken
        if not os.path.exists('plots'):
            os.makedirs('plots')

        self.fig, self.axes = plt.subplots(2, 2, figsize=(10, 8))
        self.fig.suptitle("Live Prestatie Data", fontsize=16, fontweight='bold')
        self.ani = None
    
    def add_data(self, data_dict):
        # Als gestopt is -> geen data meer toevoegen
        if self._stopped:
            return

        self.timestamps.append(datetime.now())
        for key in self.data.keys():
            if key in data_dict:
                self.data[key].append(data_dict[key])

        # Als de limit bereikt wordt, dan stopt deze met receiven
        if self._minute_counter < self.minute_limit:
            self._minute_counter += 1
        else:
            self._stopped = True
            try:
                if self.client:
                    print(f"Minute limit ({self.minute_limit}) reached — disconnecting MQTT client.")
                    self.client.disconnect()
                    # Hier wordt de loop gestopt zodat deze niet meer opnieuw begint na "90 minuten"
                    try:
                        self.client.loop_stop()
                    except Exception:
                        pass
            except Exception as e:
                print(f"Failed to disconnect client: {e}")
                    
    def update_plot(self):
        for idx, (metric, ax) in enumerate(zip(self.data.keys(), self.axes.flat)):
            ax.clear()
            if self.data[metric]:
                y = list(self.data[metric])
                x = list(range(1, len(y) + 1))

                ax.plot(x, y, marker='o', linestyle='-', linewidth=2, markersize=5)

                ax.set_title(metric.replace("_", " ").title(), fontsize=12, fontweight='bold')

                unit = self.units.get(metric, "")
                ax.set_ylabel(unit, fontsize=10)

                ax.set_xlabel("Minuut", fontsize=10)
                ax.set_xlim(1, self.minute_limit)
                ax.set_xticks(range(1, self.minute_limit + 1, max(1, self.minute_limit // 10)))

                ax.tick_params(axis='x', rotation=45)
                ax.grid(True, alpha=0.3)
        # Opslaan in een file in plaats van plt.show() aangezien Docker deze window niet kan openen
        self.fig.savefig('plots/live_grafiek.png')
        
    def start(self):
        self.fig.subplots_adjust(left=0.1, right=0.95, top=0.93, bottom=0.1, hspace=0.35, wspace=0.3)
        
        while not self._stopped:
            self.update_plot()
            time.sleep(1)
