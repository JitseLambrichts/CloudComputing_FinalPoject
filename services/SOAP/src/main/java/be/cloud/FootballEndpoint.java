package be.cloud;

// Zorg dat deze import overeenkomt met de package die JAXB heeft gegenereerd
import be.cloud.team_statistics.GetTeamStatsRequest;
import be.cloud.team_statistics.GetTeamStatsResponse;
import org.springframework.ws.server.endpoint.annotation.Endpoint;
import org.springframework.ws.server.endpoint.annotation.PayloadRoot;
import org.springframework.ws.server.endpoint.annotation.RequestPayload;
import org.springframework.ws.server.endpoint.annotation.ResponsePayload;

import java.sql.*;

@Endpoint
public class FootballEndpoint {
    // Dit MOET exact hetzelfde zijn als in je .xsd 
    private static final String NAMESPACE_URI = "http://team_statistics";

    @PayloadRoot(namespace = NAMESPACE_URI, localPart = "getTeamStatsRequest")
    @ResponsePayload
    public GetTeamStatsResponse getTeamStats(@RequestPayload GetTeamStatsRequest request) {
        GetTeamStatsResponse response = new GetTeamStatsResponse();
        
        // Verbinding met je Docker-database 
        String url = "jdbc:mysql://host.docker.internal:3306/finaletaakcloudcomputing";
        String user = "root";
        String password = "";

        try (Connection connection = DriverManager.getConnection(url, user, password)) {
            // Zoek het team op basis van de common_name uit je migratie 
            String sql = "SELECT * FROM teams WHERE common_name = ?";
            PreparedStatement statement = connection.prepareStatement(sql);
            statement.setString(1, request.getTeamName());
            ResultSet result = statement.executeQuery();

            if (result.next()) {
                // Map elk veld uit de database-migratie naar de SOAP-response 
                response.setCommonName(result.getString("common_name"));
                response.setMatchesPlayed(result.getInt("matches_played"));
                response.setWins(result.getInt("wins"));
                response.setWinsHome(result.getInt("wins_home"));
                response.setWinsAway(result.getInt("wins_away"));
                response.setLosses(result.getInt("losses"));
                // Let op: losses_home staat niet in je .xsd, dus die slaan we over
                response.setLossesAway(result.getInt("losses_away"));
                response.setDraws(result.getInt("draws"));
                response.setPointsPerGame(result.getInt("points_per_game"));
                response.setLeaguePosition(result.getInt("league_position"));
                response.setGoalsScored(result.getInt("goals_scored"));
                response.setGoalsConceded(result.getInt("goals_conceded"));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return response;
    }
}
