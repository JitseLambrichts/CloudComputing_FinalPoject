package be.cloud;

import io.grpc.stub.StreamObserver;

public class AnalyticsServiceImpl extends AnalyticsServiceGrpc.AnalyticsServiceImplBase {

    @Override
    public StreamObserver<LivePlayerUpdate> streamPlayerAnalytics(StreamObserver<AnalysisResponse> responseObserver) {
        return new StreamObserver<LivePlayerUpdate>() {
            @Override
            public void onNext(LivePlayerUpdate update) {
                // De functie die de server op de gegevens van de client toepast
                System.out.println("Ontvagen update voor: " + update.getPlayerName());
                System.out.println("Gegevens verwerken...");

                int fatigueLevel = calculateFatigueLevel(update);   // Op een schaal van 1 tot 10
                boolean shouldSubstitute = fatigueLevel >= 8;
                
                String recommendation = shouldSubstitute ? "Wissel speler" : "Speler kan nog doorspelen";
                
                AnalysisResponse response = AnalysisResponse.newBuilder()
                        .setPlayerName(update.getPlayerName())
                        .setRecommendation(recommendation)
                        .setShouldSubstitute(shouldSubstitute)
                        .setFatigueLevel(fatigueLevel)
                        .build();

                System.out.println("Gegevens succesvol verwerkt, nu doorsturen naar de client");

                responseObserver.onNext(response);
            }

            @Override
            public void onError(Throwable t) {
                System.err.println("Error in stream: " + t.getMessage());
            }

            @Override
            public void onCompleted() {
                System.out.println("Client stream gestopt");
                responseObserver.onCompleted();
            }

            private int calculateFatigueLevel(LivePlayerUpdate update) {
                // Willekeurige formule om de hartslag en lactaat-waardes te kunnen omzetten naar een enkele parameter
                int level = (int) ((update.getCurrentHeartRate() / 35) + update.getCurrentLactate() / 3);
                return Math.min(10, Math.max(1, level));
            }
        };
    }
}
