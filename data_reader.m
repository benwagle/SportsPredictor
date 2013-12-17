%Preprocessing

%function data_reader(file)

[data,txt] = xlsread('/Users/David/Desktop/Classwork/Machine Learning/Final Project/NBA edited.xlsx', 1);
%[data,txt] = xlsread(file);


%column assignments and constants for NBA data
DATE = 1;
A_TEAM = 2;
A_STATBEG = 3;
A_STATEND = 17;
H_TEAM = 18;
H_STATBEG = 19;
H_STATEND = 33;
H_RESULT = 34;
A_RESULT = 35;

NUM_TEAMS = 30;
TEAM_GAMES = 82;
NUM_STATS = H_STATEND - H_STATBEG + 2;


%replace team names with ID numbers
teams = [txt(2:end, A_TEAM), txt(2:end, H_TEAM)];

teams = strrep(teams, 'Atlanta Hawks', '1');
teams = strrep(teams, 'Boston Celtics', '2');
teams = strrep(teams, 'Charlotte Bobcats', '3');
teams = strrep(teams, 'Chicago Bulls', '4');
teams = strrep(teams, 'Cleveland Cavaliers', '5');
teams = strrep(teams, 'Dallas Mavericks', '6');
teams = strrep(teams, 'Denver Nuggets', '7');
teams = strrep(teams, 'Detroit Pistons', '8');
teams = strrep(teams, 'Golden State Warriors', '9');
teams = strrep(teams, 'Houston Rockets', '10');
teams = strrep(teams, 'Indiana Pacers', '11');
teams = strrep(teams, 'Los Angeles Clippers', '12');
teams = strrep(teams, 'Los Angeles Lakers', '13');
teams = strrep(teams, 'Memphis Grizzlies', '14');
teams = strrep(teams, 'Miami Heat', '15');
teams = strrep(teams, 'Milwaukee Bucks', '16');
teams = strrep(teams, 'Minnesota Timberwolves', '17');
teams = strrep(teams, 'New Jersey Nets', '18');
teams = strrep(teams, 'New Orleans Hornets', '19');
teams = strrep(teams, 'New York Knicks', '20');
teams = strrep(teams, 'Oklahoma City Thunder', '21');
teams = strrep(teams, 'Seattle SuperSonics', '21');
teams = strrep(teams, 'Orlando Magic', '22');
teams = strrep(teams, 'Philadelphia 76ers', '23');
teams = strrep(teams, 'Phoenix Suns', '24');
teams = strrep(teams, 'Portland Trail Blazers', '25');
teams = strrep(teams, 'Sacramento Kings', '26');
teams = strrep(teams, 'San Antonio Spurs', '27');
teams = strrep(teams, 'Toronto Raptors', '28');
teams = strrep(teams, 'Utah Jazz', '29');
teams = strrep(teams, 'Washington Wizards', '30');

teams = str2double(teams);

data(:, A_TEAM) = teams(:,1);
data(:, H_TEAM) = teams(:,2);


%sort by date
data = sortrows(data, DATE);


%compute averages
avg = zeros(NUM_TEAMS, TEAM_GAMES, NUM_STATS + 2);
game_counter = zeros(NUM_TEAMS, 1);
total = zeros(NUM_TEAMS, NUM_STATS);
WINS = H_STATEND - H_STATBEG + 2;
win_streak = zeros(NUM_TEAMS, 1);
trav_streak = zeros(NUM_TEAMS, 1);

hawkstats = zeros(82,18);

data_out = zeros(size(data,1), (2*NUM_STATS) + 8);

for i = 1:size(data,1)
    
    A_TEAM_ID = data(i, A_TEAM);
    H_TEAM_ID = data(i, H_TEAM);
    
    game_counter(A_TEAM_ID) = game_counter(A_TEAM_ID) + 1;
    game_counter(H_TEAM_ID) = game_counter(H_TEAM_ID) + 1;
    
    % calculate Away result
    data(i, A_RESULT) = -1 * data(i, H_RESULT);
    
    % calculate number of wins
    if data(i, A_RESULT) == 1
        A_WIN = 1;
        H_WIN = 0;
    else
        A_WIN = 0;
        H_WIN = 1;
    end
    
    
    % calculate win streak
    if win_streak(A_TEAM_ID) * data(i, A_RESULT) >= 0
        win_streak(A_TEAM_ID) = win_streak(A_TEAM_ID) + data(i, A_RESULT);
    else
        win_streak(A_TEAM_ID) = data(i, A_RESULT);
    end
    if win_streak(H_TEAM_ID) * data(i, H_RESULT) >= 0
        win_streak(H_TEAM_ID) = win_streak(H_TEAM_ID) + data(i, H_RESULT);
    else
        win_streak(H_TEAM_ID) = data(i, H_RESULT);
    end  
    
    % calculate travel streak
    if trav_streak(A_TEAM_ID) <= 0
        trav_streak(A_TEAM_ID) = trav_streak(A_TEAM_ID) - 1;
    else
        trav_streak(A_TEAM_ID) = -1;
    end
    if trav_streak(H_TEAM_ID) >= 0
        trav_streak(H_TEAM_ID) = trav_streak(H_TEAM_ID) + 1;
    else
        trav_streak(H_TEAM_ID) = 1;
    end
    
    
    total(A_TEAM_ID, :) = total(A_TEAM_ID, :) + [data(i, A_STATBEG:A_STATEND), A_WIN];
    total(H_TEAM_ID, :) = total(H_TEAM_ID, :) + [data(i, H_STATBEG:H_STATEND), H_WIN]; 
     
    
    if A_TEAM_ID == 1
        hawkstats(game_counter(A_TEAM_ID),:) = [data(i, A_STATBEG:A_STATEND), A_WIN, win_streak(1), trav_streak(1)];
    end
    if H_TEAM_ID == 1
        hawkstats(game_counter(H_TEAM_ID),:) = [data(i, H_STATBEG:H_STATEND), H_WIN, win_streak(1), trav_streak(1)];
    end
    
    
    avg(A_TEAM_ID, game_counter(A_TEAM_ID), :) = [(1/game_counter(A_TEAM_ID)) * total(A_TEAM_ID, :), win_streak(A_TEAM_ID), trav_streak(A_TEAM_ID)];
    avg(H_TEAM_ID, game_counter(H_TEAM_ID), :) = [(1/game_counter(H_TEAM_ID)) * total(H_TEAM_ID, :), win_streak(H_TEAM_ID), trav_streak(H_TEAM_ID)];
    
    
    data_out(i,:) = [data(i, DATE), H_TEAM_ID, A_TEAM_ID, data(i, H_RESULT), reshape(avg(H_TEAM_ID, game_counter(H_TEAM_ID), :), [1 18]), reshape(avg(A_TEAM_ID, game_counter(A_TEAM_ID), :), [1 18])];  
end


%check on Hawks
hawksavg = reshape(avg(1, :, :), [82 18]);
check = hawkstats;
for i=2:size(check, 1)
    check(i,1:16) = (1/i) * ((check(i-1,1:16)*(i-1))+check(i,1:16));
end
equal = hawksavg - check;

%dlmwrite('mat_out.txt', data_out);

%end



