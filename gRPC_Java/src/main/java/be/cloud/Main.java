package be.cloud;

import io.grpc.Server;
import io.grpc.ServerBuilder;

import java.io.IOException;

public class Main {
    public static void main(String[] args) throws IOException, InterruptedException {
        System.out.println("Starten van Voetbal Analytics gRPC server...");

        Server server = ServerBuilder.forPort(50051)
                .addService(new AnalyticsServiceImpl())
                .build();

        server.start();
        Runtime.getRuntime().addShutdownHook(new Thread(() -> {
            System.err.println("Shutting down gRPC server...");
            server.shutdown();
        }));
        server.awaitTermination();
    }
}
