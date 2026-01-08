# Service - MQTT (JavaScript)

Deze service stelt de data van de spelersprestatie voor. Deze subscribed op het topic 'prestatie' om info te ontvangen over verschillende metingen (zie bijlage A). Deze zal maximum 90 metingen ontvangen en dan zal deze unsubscriben. Dit gebeurd aangezien een match 90 minuten duurt (en elke meting stelt een minuut voor). Deze zal de gegevens ook doorsturen naar de gRPC server zodat deze hier een analyse op kan maken.
Als de 90 berichten zijn ontvangen, dan zal er een finaal bericht te zien zijn dat de samenvatting laat zien.

De sender is niet aangepast geweest buiten de settings.config (dus deze staat nog in Python).

Endpoint: HiveMQ

# Bijlage
### Bijlage A
Veschillende meetwaardes:
- Hartslag                  (bpm)
- Systolische-bloeddruk     (mmol/L)
- Lactaat-waardes           (mmHg)
- Zuurstof-opname           (ml/min)
- Hartminuutvolume          (L/min)
- Maximale belasting        (W)
- Anaerobe drempel          (ml/min)