# Stage 1: Build met Maven --> om mvn clean package te doen voor elke docker compose
FROM maven:3.9-eclipse-temurin-21 AS builder
WORKDIR /app

COPY . .
RUN mvn clean package -DskipTests

# Stage 2: Docker voor Java
FROM eclipse-temurin:21-jre
WORKDIR /app

COPY --from=builder /app/target/grpc.jar /app/grpc.jar

EXPOSE 50051

CMD ["java", "-jar", "grpc.jar"]
