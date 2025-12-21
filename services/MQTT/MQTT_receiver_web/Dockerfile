FROM node:18-alpine

WORKDIR /app

# Kopieer package files
COPY package*.json ./

# Installeer dependencies
RUN npm install

# Kopieer de rest van de applicatie
COPY . .

# Expose de WebSocket poort
EXPOSE 9292

# Start de applicatie
CMD ["node", "mqtt.js"]