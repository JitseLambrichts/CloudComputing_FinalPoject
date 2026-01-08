/*
	Deze file is verantwoordelijk voor het analyseren van de gegevens van MQTT
*/

package main

import (
	pb "grpc_go/analytics"
)

// Server struct die de AnalyticsService interface implementeert
type server struct {
	pb.UnimplementedAnalyticsServiceServer
}

// Algemene implementatie van gRPC
// Deze service zal de gegevens van MQTT ontvangen en op basis hiervan een analyse maken
// Verder zal deze ook de gemiddelde waardes berekenen en op het einde een overzicht geven als de 90 minuten (match-simulatie) voorbij zijn
func (s *server) StreamPlayerAnalytics(stream pb.AnalyticsService_StreamPlayerAnalyticsServer) error {
	var totalHeartRate int64 = 0;
	var totalLactate float64 = 0;
	var messageCount int32 = 0;

	for {
		req, err := stream.Recv()

		// Als er een error gegeven wordt wil dit zeggen dat de stream beïndigd is -> dan de final message
		if err != nil {
			if messageCount > 0 {
				avgHR := float32(totalHeartRate) / float32(messageCount)
				avgLac := float32(totalLactate) / float32(messageCount)

				finalResp := &pb.AnalysisResponse{
					Recommendation: "Match simulation complete",
					ShouldSubstitute: false,
					FatigueLevel: 0,
					AvgHeartRate: avgHR,
					AvgLactate: avgLac,
					TotalMessages: messageCount,
					IsFinalSummary: true,
				}
				stream.Send(finalResp)
			}
			return err
		}

		totalHeartRate += int64(req.CurrentHeartRate)
		totalLactate += float64(req.CurrentLactate)
		messageCount++

		// Dit is de vermoeidheid op een schaal van 1 tot 10 (met 1 niet vermoeid, en 10 heel erg vermoeid)
		fatigue := calucalteFatigueLevel(req.CurrentHeartRate, req.CurrentLactate)
		recommendation := "Speler hoeft niet te wisselen"
		shouldSub := false
		
		if fatigue > 8 {
			recommendation = "Speler moet gewisseld worden"
			shouldSub = true
		}

		resp := &pb.AnalysisResponse{
			Recommendation: recommendation,
			ShouldSubstitute: shouldSub,
			FatigueLevel: int32(fatigue),
			AvgHeartRate: float32(totalHeartRate) / float32(messageCount),
			AvgLactate: float32(totalLactate) / float32(messageCount),
			TotalMessages: messageCount,
			IsFinalSummary: false,
		}

		if err := stream.Send(resp); err != nil {
			return err
		}
	}
}

// Helper functie om de vermoeidheid te berekenen (tussen 0 en 10)
func calucalteFatigueLevel(heartRate int32, lactate float32) int32 {
	// Berekenen van vermoeidheids niveau op basis een "willekeurige" formule (moet gewoon lineair zijn met de hartslag en lactaat-waardes)
    fatigue := int(heartRate/25) + int(lactate/3)

    if fatigue > 10 {
        return 10
    }

    return int32(fatigue)
}