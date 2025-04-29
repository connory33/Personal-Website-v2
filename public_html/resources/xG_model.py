import pandas as pd
import numpy as np
from sklearn import metrics
from sklearn.model_selection import train_test_split
from sklearn.linear_model import LogisticRegression
from sklearn.metrics import accuracy_score, roc_auc_score, confusion_matrix, classification_report
import seaborn as sns
from sklearn.ensemble import RandomForestClassifier
from datetime import timedelta
import matplotlib.pyplot as plt

# Function to convert MM:SS to seconds, handling NaN values
def mmss_to_seconds(mmss):
    mmss = str(mmss)  # Ensure mmss is a string
    if pd.isna(mmss):
        return np.nan  # Return NaN if the value is NaN
    else:
        minutes, seconds = map(int, mmss.split(':'))
        return minutes * 60 + seconds

# Function to convert total seconds to MM:SS format
def mmss_to_seconds(mmss):
    try:
        if isinstance(mmss, str) and ':' in mmss:
            minutes, seconds = map(int, mmss.split(':'))
            return minutes * 60 + seconds
        elif mmss == '0' or mmss == 0 or pd.isna(mmss):
            return 0
    except Exception:
        pass
    return 0  # fallback if bad format






# Load your shot datapi
data = pd.read_csv("C:/Users/conno/OneDrive/Documents/Personal-and-NHL-Website/public_html/resources/data/nhl_PbP.csv")
# print(data.head(10))
data = data.fillna(0)

# Additional feature engineering like shot type, player skill, etc.

# Target variable: 1 for goal, 0 for non-goal
data['goal'] = np.where(data['typeDescKey'] == 'goal', 1, 0)



# Plot distribution
# sns.histplot(data=data, x='distance', hue='goal', bins=50, stat="density", common_norm=False)
# plt.title('Shot Distance Distribution by Goal/No Goal')
# plt.show()


### Feature engineering ###

left_net_xCoord = -89.0
right_net_xCoord = 89.0
left_net_yCoord = 0.0
right_net_yCoord = 0.0

# Remove bad rows
data = data[data['xCoord'] != 'No data available']
data = data[data['yCoord'] != 'No data available']

# Convert types
data['xCoord'] = data['xCoord'].astype(float)
data['yCoord'] = data['yCoord'].astype(float)
data['duration'] = data['duration'].astype(float)  # Convert to float first if needed
# data['duration'] = data['duration'].astype(int)

# Distances - correctly written
data['distance_from_left_net'] = np.sqrt((data['xCoord'] - left_net_xCoord)**2 + (data['yCoord'] - left_net_yCoord)**2)
data['distance_from_right_net'] = np.sqrt((data['xCoord'] - right_net_xCoord)**2 + (data['yCoord'] - right_net_yCoord)**2)

# Pick the correct distance depending on side defending - NEED TO CHANGE THIS,IT'S ALWAYS ASSUMING HOME TEAM IS SHOOTING
data['relevant_distance'] = np.where(
    data['homeTeamDefendingSide'] == 'left',
    data['distance_from_left_net'],
    np.where(
        data['homeTeamDefendingSide'] == 'right',
        data['distance_from_right_net'],
        np.nan  # if missing or weird value
    )
)

# Also calculate "relevant_angle_num" safely
data['relevant_angle_num'] = np.where(
    data['homeTeamDefendingSide'] == 'left',
    data['xCoord'] - (-89),
    np.where(
        data['homeTeamDefendingSide'] == 'right',
        89 - data['xCoord'],
        np.nan
    )
)

# Get distance features
data['distance'] = data['relevant_distance']
data['angle'] = np.arccos(data['relevant_angle_num'] / data['relevant_distance'])
data['log_distance'] = np.log1p(data['distance'])  # log transform
data['sin_angle'] = np.sin(data['angle'])          # sin of angle

data['homeTeamDefendingSide'] = data['homeTeamDefendingSide'].map({'left': 0, 'right': 1})

# Get type of previous event
data = data.sort_values(by=['gameID', 'eventID'])  # Use appropriate column name for event order
data['prev_typeDescKey'] = data.groupby('gameID')['typeDescKey'].shift(1)  # Shift within each game

# create specific feature for rebound
data['rebound'] = np.where(data['prev_typeDescKey'] == 'shot', 1, 0)
print(data['timeRemaining'])

data['timeRemaining'] = data['timeRemaining'].apply(mmss_to_seconds)  # Convert to seconds
# data['timeRemaining'] = data['timeRemaining'].astype(int)  # Ensure it's an integer

# Strength state of game (5v5, 4v4, etc.)
data['penaltyStartTime'] = np.where(data['duration'].notnull(), data['timeRemaining'], np.nan)
data['penaltyEndTime'] = np.where(data['duration'].notnull(), data['timeRemaining'] - data['duration'], np.nan)
data['penalty_active'] = (data['timeRemaining'] >= data['penaltyStartTime']) & (data['timeRemaining'] <= data['penaltyEndTime'])



# Function to adjust penalty based on goal logic
def adjust_penalty_based_on_goal(row, last_goal_time):
    # If a goal is scored
    if row['goal'] == 1:
        # Case 1: 2-minute penalty duration should become inactive after a goal
        if row['duration'] == 2:
            return False  # Penalty is no longer active
        
        # Case 2: 5-minute penalty duration remains active after a goal
        elif row['duration'] == 5:
            return True  # Penalty remains active
        
        # Case 3: 4-minute penalty logic based on when the goal occurs
        elif row['duration'] == 4:
            if row['timeRemaining'] >= 2:  # Goal scored within first 2 minutes
                return True  # Penalty resets to 2 minutes starting from goal
            else:  # Goal scored after 2 minutes
                return False  # Penalty ends

    # If no goal was scored, the penalty stays active as long as timeRemaining is positive
    if row['timeRemaining'] > 0:
        return True  # Penalty is still active
    
    return False  # Penalty is not active

# Apply logic to adjust penalty active-ness based on goal events
last_goal_time = None  # Track the last goal time
data['penalty_active'] = False  # Initialize the column for tracking penalty active-ness

for idx, row in data.iterrows():
    # Update the penalty_active status based on the goal
    data.at[idx, 'penalty_active'] = adjust_penalty_based_on_goal(row, last_goal_time)
    
    # If a goal is scored, update the last goal time (this will allow us to track when the goal happens)
    if row['goal'] == 1:
        last_goal_time = row['timeRemaining']  # Set the goal time

# After this, you can reassign the strength column based on the adjusted penalty status
data['strength'] = data['penalty_active'].apply(lambda x: 'PK' if x else 'EV')




# time of event before/time betweend events
# player skill (shot pct)
# distance between prev event and current (could indicate pass)

# One hot encoding for categorical variables
for col in ['typeDescKey', 'shotType', 'penaltyType', 'prev_typeDescKey']:
    if col in data.columns:
        data = pd.get_dummies(data, columns=[col], drop_first=True)
    else:
        print(f"{col} not found in data")


# Features 'shotType_backhand',
features = ['xCoord', 'yCoord', 'homeTeamDefendingSide', 'distance', 'angle', 'shotType_wrist', 'shotType_snap', 'shotType_slap',  'shotType_tip-in', 'shotType_wrap-around', 
            'log_distance', 'sin_angle', 'strength'] + \
           [col for col in data.columns if col.startswith('prev_typeDescKey')]  # Add the one-hot encoded columns dynamically
X = data[features]
y = data['goal']

# Train-test split
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)


############################################################################# LOGISTIC REGRESSION CLASSIFIER #########################################################################

# # Initialize and train logistic regression model
# model = LogisticRegression(class_weight='balanced')
# model.fit(X_train, y_train)

# # Predictions
# # Predict on the test set
# y_pred = model.predict(X_test)

# # If you used a classifier with probabilities (like LogisticRegression)
# y_pred_prob = model.predict_proba(X_test)[:, 1]

# # Evaluate the model
# from sklearn.metrics import roc_auc_score, accuracy_score, confusion_matrix, classification_report

# from sklearn.metrics import roc_auc_score, accuracy_score, confusion_matrix, classification_report

# print("Accuracy:", accuracy_score(y_test, y_pred))
# print("ROC-AUC:", roc_auc_score(y_test, y_pred_prob))
# print(confusion_matrix(y_test, y_pred))
# print(classification_report(y_test, y_pred))

# from sklearn.calibration import calibration_curve

# prob_true, prob_pred = calibration_curve(y_test, y_pred_prob, n_bins=10)

# plt.plot(prob_pred, prob_true, marker='o')
# plt.plot([0, 1], [0, 1], linestyle='--')
# plt.title('Calibration Curve')
# plt.xlabel('Predicted Probability')
# plt.ylabel('True Probability')
# plt.show()

# import matplotlib.pyplot as plt

# # Plot the distribution of predicted probabilities
# plt.hist(y_pred_prob, bins=50, range=(0, 1), alpha=0.75)
# plt.title('Distribution of Predicted Probabilities')
# plt.xlabel('Predicted Probability')
# plt.ylabel('Frequency')
# plt.show()


####################################################################### RANDOM FOREST CLASSIFIER #######################################################################

# model_rf = RandomForestClassifier(class_weight='balanced', random_state=42)
# model_rf.fit(X_train, y_train)

# # Predictions
# y_pred_rf = model_rf.predict(X_test)
# y_pred_prob_rf = model_rf.predict_proba(X_test)[:, 1]

# # Adjust the threshold for classification
# threshold = 0.1
# y_pred = (y_pred_prob_rf >= threshold).astype(int)


# # Plot the distribution of predicted probabilities
# plt.hist(y_pred_prob_rf, bins=50, range=(0, 1), alpha=0.75)
# plt.title('Distribution of Predicted Probabilities')
# plt.xlabel('Predicted Probability')
# plt.ylabel('Frequency')
# plt.show()

# # Evaluate
# print("Accuracy:", accuracy_score(y_test, y_pred_rf))
# print("ROC-AUC:", roc_auc_score(y_test, y_pred_prob_rf))
# print(confusion_matrix(y_test, y_pred_rf))
# print(classification_report(y_test, y_pred_rf))



########################################################################## XGBOOST CLASSIFIER #########################################################################
from xgboost import XGBClassifier
from sklearn.model_selection import GridSearchCV

# Calculate class imbalance ratio
positive_class_count = sum(y == 1)  # Number of goals
negative_class_count = sum(y == 0)  # Number of non-goals

scale_pos_weight = negative_class_count / positive_class_count

params = {
    'n_estimators': 200,              # Number of boosting rounds (trees)
    'learning_rate': 0.05,             # Step size for each boosting round
    'max_depth': 5,                   # Maximum depth of a tree
    'subsample': 0.7,                 # Subsample ratio of the training set
    'scale_pos_weight': scale_pos_weight,  # Adjust for class imbalance
}

model = XGBClassifier(**params)  # adjust scale_pos_weight based on class imbalance
# param_grid = {
#     'learning_rate': [0.01, 0.05, 0.1],
#     'n_estimators': [50, 100, 200],
#     'max_depth': [3, 5, 7],
#     'subsample': [0.7, 0.8, 1.0],
# }
# grid_search = GridSearchCV(model, param_grid, scoring='roc_auc', cv=5)
# grid_search.fit(X_train, y_train)

# best_model = grid_search.best_estimator_
# y_pred_best = best_model.predict(X_test)
# y_pred_prob_best = best_model.predict_proba(X_test)[:, 1]

# print("Best Parameters:", grid_search.best_params_)



model.fit(X_train, y_train)

y_pred_xg = model.predict(X_test)
y_pred_prob_xg = model.predict_proba(X_test)[:, 1]

threshold = 0.15
y_pred_xg = (y_pred_prob_xg >= threshold).astype(int)

# Plot the distribution of predicted probabilities
plt.hist(y_pred_prob_xg, bins=50, range=(0, 1), alpha=0.75)
plt.title('Distribution of Predicted Probabilities')
plt.xlabel('Predicted Probability')
plt.ylabel('Frequency')
plt.show()

print("Accuracy:", accuracy_score(y_test, y_pred_xg))
print("ROC-AUC:", roc_auc_score(y_test, y_pred_prob_xg))
print(confusion_matrix(y_test, y_pred_xg))
print(classification_report(y_test, y_pred_xg))

# # Plot distance vs goal
# sns.scatterplot(x='distance', y='goal', data=data)
# plt.title('Distance vs Goal')
# plt.xlabel('Distance')
# plt.ylabel('Goal (1 = Goal, 0 = No Goal)')
# plt.show()

# # Plot angle vs goal
# sns.scatterplot(x='angle', y='goal', data=data)
# plt.title('Angle vs Goal')
# plt.xlabel('Angle')
# plt.ylabel('Goal (1 = Goal, 0 = No Goal)')
# plt.show()





