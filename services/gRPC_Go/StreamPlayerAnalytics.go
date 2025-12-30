package main

import (
	pb "grpc_go/analytics"
)

type server struct {
	pb.UnimplementedAnalyticsServiceServer
}

func (s *server) StreamPlayerAnalytics(stream pb.AnalyticsService_StreamPlayerAnalyticsServer) error {
	var totalHeartRate int64 = 0;
	var totalLactate float64 = 0;
	var messageCount int32 = 0;

	for {
		req, err := stream.Recv()
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

		// broadCastToWebsocket(resp)
	}
}

func calucalteFatigueLevel(heartRate int32, lactate float32) int32 {
    // Bereken fatigue: hartslag/25 + lactaat/3, met maximum van 10
    fatigue := int(heartRate/25) + int(lactate/3)
    if fatigue > 10 {
        return 10
    }
    return int32(fatigue)
}