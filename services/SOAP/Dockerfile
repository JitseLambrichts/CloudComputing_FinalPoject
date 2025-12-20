# Stap 1: Bouw de applicatie met Maven
FROM maven:3.9.5-eclipse-temurin-21-alpine AS build
WORKDIR /app

# Kopieer de configuratie en broncode
COPY pom.xml .
COPY src ./src

# Bouw de JAR file (dit maakt de target/ map aan IN de container)
RUN mvn clean package -DskipTests

# Stap 2: Maak de uiteindelijke runtime image
FROM eclipse-temurin:21-jdk-alpine
WORKDIR /app

# Kopieer de JAR vanuit de vorige 'build' stap (niet van je eigen computer)
COPY --from=build /app/target/SOAP-1.0-SNAPSHOT.jar soap.jar

EXPOSE 8080
ENTRYPOINT ["java", "-jar", "soap.jar"]