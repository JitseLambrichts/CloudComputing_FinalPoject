package be.cloud;

import be.cloud.team_statistics.GetTeamStatsRequest;
import be.cloud.team_statistics.GetTeamStatsResponse;
import be.cloud.team_statistics.GetPlayerStatsRequest;
import be.cloud.team_statistics.GetPlayerStatsResponse;
import be.cloud.team_statistics.UpdatePlayerMinutesRequest;
import be.cloud.team_statistics.UpdatePlayerMinutesResponse;
import org.springframework.ws.server.endpoint.annotation.Endpoint;
import org.springframework.ws.server.endpoint.annotation.PayloadRoot;
import org.springframework.ws.server.endpoint.annotation.RequestPayload;
import org.springframework.ws.server.endpoint.annotation.ResponsePayload;

import java.sql.*;
import java.time.LocalDate;
import java.time.Period;
import java.time.format.DateTimeFormatter;

@Endpoint
public class FootballEndpoint {
    // Dit MOET exact hetzelfde zijn als in je .xsd 
    private static final String NAMESPACE_URI = "http://be/cloud/team_statistics";

    private static final String DATABASE_URL = System.getenv("DB_URL");
    private static final String DATABASE_USER = System.getenv("DB_USERNAME");
    private static final String DATABASE_PASSWORD = System.getenv("DB_PASSWORD");

    @PayloadRoot(namespace = NAMESPACE_URI, localPart = "getTeamStatsRequest")
    @ResponsePayload
    public GetTeamStatsResponse getTeamStats(@RequestPayload GetTeamStatsRequest request) {
        GetTeamStatsResponse response = new GetTeamStatsResponse();
        
        String url = DATABASE_URL;
        String user = DATABASE_USER;
        String password = DATABASE_PASSWORD;

        try (Connection connection = DriverManager.getConnection(url, user, password)) {
            // Zoek het team op basis van de common_name uit je migratie 
            String sql = "SELECT * FROM teams WHERE common_name = ?";
            PreparedStatement statement = connection.prepareStatement(sql);
            statement.setString(1, request.getTeamName());
            ResultSet result = statement.executeQuery();

            if (result.next()) {
                response.setId(result.getInt("id"));
                response.setCommonName(result.getString("common_name"));
                response.setMatchesPlayed(result.getInt("matches_played"));
                response.setWins(result.getInt("wins"));
                response.setWinsHome(result.getInt("wins_home"));
                response.setWinsAway(result.getInt("wins_away"));
                response.setLosses(result.getInt("losses"));
                response.setLossesHome(result.getInt("losses_home"));
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

    @PayloadRoot(namespace = NAMESPACE_URI, localPart = "getPlayerStatsRequest")
    @ResponsePayload
    public GetPlayerStatsResponse getPlayerStats(@RequestPayload GetPlayerStatsRequest request) {
        GetPlayerStatsResponse response = new GetPlayerStatsResponse();

        String url = DATABASE_URL;
        String user = DATABASE_USER;
        String password = DATABASE_PASSWORD;

        try (Connection connection = DriverManager.getConnection(url, user, password)) {
            String query = "SELECT * FROM players WHERE full_name = ?";
            PreparedStatement statement = connection.prepareStatement(query);
            statement.setString(1, request.getPlayerName());
            ResultSet result = statement.executeQuery();

            if (result.next()) {
                response.setId(result.getInt("id"));
                response.setFullName(result.getString("full_name"));
                String birthdayString = result.getString("birthday_GMT");
                response.setBirthdayGMT(birthdayString);
                response.setAge(calculateAge(birthdayString));
                response.setPosition(result.getString("position"));
                response.setTeamId(result.getInt("team_id"));
                response.setMinutesPlayedOverall(result.getInt("minutes_played_overall"));
                response.setNationality(result.getString("nationality"));
                response.setGoalsOverall(result.getInt("goals_overall"));
                response.setAssistsOverall(result.getInt("assists_overall"));
                response.setYellowCardsOverall(result.getInt("yellow_cards_overall"));
                response.setRedCardsOverall(result.getInt("red_cards_overall"));
            }
        } catch (SQLException e) {
            e.printStackTrace();
        }
        return response;
    }

    private int calculateAge(String birthdayString) {
        if (birthdayString != null && !birthdayString.isEmpty()) {
            LocalDate birthday = LocalDate.parse(birthdayString, DateTimeFormatter.ofPattern("yyyy/MM/dd"));
            return Period.between(birthday, LocalDate.now()).getYears();
        }
        return 0;
    }

    @PayloadRoot(namespace = NAMESPACE_URI, localPart = "updatePlayerMinutesRequest")
    @ResponsePayload
    public UpdatePlayerMinutesResponse updatePlayerMinutesResponse(@RequestPayload UpdatePlayerMinutesRequest request) {
        UpdatePlayerMinutesResponse response = new UpdatePlayerMinutesResponse();

        String url = DATABASE_URL;
        String user = DATABASE_USER;
        String password = DATABASE_PASSWORD;

        try (Connection connection = DriverManager.getConnection(url, user, password)) {
            String update = "UPDATE players SET minutes_played_overall = minutes_played_overall + ? WHERE full_name = ?";
            PreparedStatement updateStatement = connection.prepareStatement(update);
            updateStatement.setInt(1, request.getMinutesToAdd());
            updateStatement.setString(2, request.getPlayerName());
            int rowsAffected = updateStatement.executeUpdate();

            if (rowsAffected > 0) {
                String query = "SELECT minutes_played_overall FROM players WHERE full_name = ?";
                PreparedStatement queryStatement = connection.prepareStatement(query);
                queryStatement.setString(1, request.getPlayerName());
                ResultSet result = queryStatement.executeQuery();

                if (result.next()) {
                    response.setSucces(true);
                    response.setMessage(request.getMinutesToAdd() + " minuten toegevoegd aan " + request.getPlayerName());
                    response.setNewMinutesTotal(result.getInt("minutes_played_overall"));
                }
            } else {
                response.setSucces(false);
                response.setMessage("Speler niet gevonden: " + request.getPlayerName());
                response.setNewMinutesTotal(0);
            }
        } catch (SQLException e) {
            e.printStackTrace();
            response.setSucces(false);
            response.setMessage("Error met de database: " + e.getMessage());
            response.setNewMinutesTotal(0);
        }
        return response;
    }
}
