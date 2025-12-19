from mqtt_functies import *

#voorbeeld van een regelmatige publish
import time
def send_messages(client):
    for i in range(1,10):
        client.publish("my_topic", "msg " + str(i))
        time.sleep(0.25)

if __name__ == '__main__':
    print("************")
    print("**  MQTT  **")
    print("************")

    # Nodig voor de grafiek te plotten
    import mqtt_functies
    mqtt_functies.live_plotter = LivePlotter(max_points=90)

    client = create_client()
    mqtt_functies.live_plotter.client = client
    
    client.on_connect = report_connect_status
    # client.on_publish = on_publish

    client.on_subscribe = on_subscribe
    client.on_message = on_message

    client.subscribe("prestatie")

    client.loop_start()

    # Start de plotter (dit blokkeert tot het venster wordt gesloten)
    mqtt_functies.live_plotter.start()
    
    print("Shutting down...")
    client.disconnect()

