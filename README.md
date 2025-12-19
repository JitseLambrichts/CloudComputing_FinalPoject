# Project Cloud Computing: Voetbalmanager

Dit project simuleert een voetbalmanager. Hierbij kunnen gebruikers teams opvragen. Van deze teams worden dan de wedstrijden, spelers, uitslagen getoond. 

## Services:
### gRPC (Matchdata-analyse)
Voor de analyse van wedstrijddata wordt gRPC gebruikt. De client stuurt de gegevens (hartslag, lactaatwaardes) van de spelers door naar de server. De server reageert dan met een indicatie of de speler gewisseld moet worden op basis van zijn vermoeidheid.