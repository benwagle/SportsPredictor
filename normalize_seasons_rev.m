data = dlmread('/Users/David/Desktop/Classwork/Machine Learning/Final Project/data.txt');

% decide which columns to include
% Home result: 1              Away: 20  
% Home ESPN: 2:16                   21:35
% Home win percentange: 17          36
% Home win streak: 18               37
% Home travel streak: 19            38

used_data = [data(:,1:19), data(:,21:38)];

training = used_data(1:1230*3,:);
test = used_data((1230*3)+1:end,:);

%normalize between 0 and 1
for i = 1:size(used_data,2)
    training(:,i) = mat2gray(training(:,i));
    test(:,i) = mat2gray(test(:,i));
end

%negative away results
away_start = (size(used_data,2)/2) + 1.5;
training(:, away_start:end) = -1 * training(:, away_start:end);
test(:, away_start:end) = -1 * test(:, away_start:end);


dlmwrite('train_ESPN_extra_unnorm.txt', training);
dlmwrite('test_ESPN_extra_unnorm.txt', test);
