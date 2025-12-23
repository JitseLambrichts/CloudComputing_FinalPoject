package main

import (
	pb "grpc_go/analytics"

	"context"
)

type server struct {
	pb.UnimplementedAnalyticsServiceServer
}

func (s *server) StreamPlayerAnalytics(stream pb.AnalyticsService_StreamPlayerAnalyticsServer) error {
	for {
		req, err := stream.Recv()
		if err != nil {
			return err
		}

		fatigue := calucalteFatigueLevel(req.CurrentHeartRate, req.CurrentLactate)
		recommendation := "Speler hoeft niet te wisselen"
		shouldSub := false
		
		if fatigue > 8 {
			recommendation = "Speler moet gewisseld worden"
			shouldSub = true
		}

		resp := &pb.AnalysisResponse{
			PlayerName: req.PlayerName,
			Recommendation: recommendation,
			ShouldSubstitute: shouldSub,
			FatigueLevel: int32(fatigue),
		}

		if err := stream.Send(resp); err != nil {
			return err
		}

		// broadCastToWebsocket(resp)
	}
}

func (s *server) AnalyzePlayer(ctx context.Context, request *pb.LivePlayerUpdate) (*pb.AnalysisResponse, error) {
	fatigue := calucalteFatigueLevel(request.CurrentHeartRate, request.CurrentLactate)
	recommendation := "Speler hoeft niet te wisselen"
	shouldSub := false

	if fatigue >= 8 {
		recommendation = "Speler moet gewisseld worden"
		shouldSub = true;
	}

	return &pb.AnalysisResponse{
		PlayerName: request.PlayerName,
		Recommendation: recommendation,
		ShouldSubstitute: shouldSub,
		FatigueLevel: int32(fatigue),
	}, nil
}

func calucalteFatigueLevel(heartRate int32, lactate float32) int32 {
    // Bereken fatigue: hartslag/25 + lactaat/3, met maximum van 10
    fatigue := int(heartRate/25) + int(lactate/3)
    if fatigue > 10 {
        return 10
    }
    return int32(fatigue)
}