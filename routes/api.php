<?php


/**
 * AUTH
 */
Route::group(['prefix' => ''], function (){
    Route::post('auth/login', 'AuthController@login');
    Route::post('auth/register', 'AuthController@register');
    Route::post('auth/registerCompany', 'AuthController@registerCompany');
    Route::post('auth/registerClub', 'AuthController@registerClub');
    Route::get('auth/activation/{token}', 'AuthController@verifyToken');
    Route::post('auth/forgotPassword', 'AuthController@forgotPassword');
    Route::post('auth/changePassword', 'AuthController@changePassword');
    Route::post('auth/newPassword/{token}', 'AuthController@newPassword');
    Route::post('auth/emailCheck', 'AuthController@emailCheck');
    Route::get('logout', 'AuthController@logout');
    Route::get('me', 'AuthController@me');
});


/**
 * USER
 */
Route::group(['prefix' => ''], function (){
    Route::apiResource('user', 'UserController');
    Route::apiResource('userHistories', 'UserHistoryController')->only(['index', 'store']);
    Route::delete('userHistories', 'UserHistoryController@destoryHistories');
    Route::get('user/{id}/activities', 'UserController@userActivities');
    Route::get('userReceiptDates', 'UserController@userReceiptDates');
    Route::get('userName/{user}', 'UserController@userName');
    Route::get('userEdit', 'UserController@getUserEdit');
    Route::get('userSubscriptions', 'CompanyController@userCompanies');
    Route::get('myXpPurchase', 'UserController@myXpPurchase');
});


/**
 * GROUP
 */
Route::group(['prefix' => ''], function (){
    Route::apiResource('group','GroupController');
    Route::get('group/{id}/activities', 'GroupController@groupActivities');
    Route::get('group/{id}/{type}','GroupController@groupDetail');
    Route::post('requestMembership', 'GroupController@requestMembership');
    Route::get('pendingMemberships/{id}', 'GroupController@getPendingMemberships');
    Route::post('acceptMembershipRequest', 'GroupController@acceptMembershipRequest');
    Route::get('acceptedMemberships/{id}', 'GroupController@getAcceptedMemberships');
    Route::post('cancelMembership', 'GroupController@cancelMembership');
    Route::post('leaveMembership', 'GroupController@leaveMembership');
    Route::get('myGroups/{type}', 'GroupController@myGroups');
    Route::get('userMemberships/{type}', 'GroupController@userMemberships');

});


/**
 * INVITE
 */
Route::group(['prefix' => ''], function (){
    Route::post('sendInvite', 'InviteController@sendInvite');
    Route::post('acceptInvite', 'InviteController@acceptInvite');
    Route::get('inviteFriendsGroup/{id}', 'InviteController@inviteFriendsGroup');

    });

/**
 * COMPANY
 */
Route::group(['prefix' => ''], function (){
    Route::apiResource('company', 'CompanyController');
    Route::get('companySubscribers/{id}', 'CompanyController@getCompanySubscribers');
    Route::get('activitiesByCompany/{id}', 'CompanyController@activitiesByCompanyId');
    Route::get('activitiesOfCompanyByUser', 'CompanyController@getActivitiesOfCompany');
    Route::get('subscribeCompany/{id}', 'CompanyController@subscribeCompany');
    Route::delete('unSubscribeCompany/{id}', 'CompanyController@unsubscribeCompany');
    Route::get('activitiesOfCompany', 'CompanyController@activitiesOfCompany');
});


/**
 * ACTIVITY
 */
Route::group(['prefix' => ''], function (){
    Route::apiResource('activity', 'ActivityController');
    Route::apiResource('activityComment', 'ActivityCommentController');
    Route::apiResource('saveActivity', 'ActivitySaveController')->only(['index', 'store', 'destroy']);
    Route::get('activityEdit/{id}', 'ActivityController@getActivityEdit');
    Route::delete('destroyAll/{parent_id}', 'ActivityController@destroyAll');
    Route::get('activity/{id}/occurrences', 'ActivityController@activityOccurrences');
    Route::get('activities', 'ActivityController@activities');
    Route::get('activitiesWithDetail', 'ActivityController@activitiesWithDetail');
    Route::get('activitiesByCity', 'ActivityController@getActivitiesByCity');
    Route::get('startRecommendedActivities', 'ActivityController@startRecommendedActivities');
    Route::get('bookedActivities', 'ActivityController@getBookedActivities');
    Route::get('userHistoryActivities', 'ActivityController@getParticipatedActivities');
    Route::get('activitiesHistoryByUser/{id}', 'ActivityController@getParticipatedActivitiesByUser');
    Route::get('activityComments/{id}', 'ActivityCommentController@getActivityComments');
    Route::get('companyFromMyCountry', 'ActivityController@getCompaniesFromMyCountry');
    Route::get('inviteUnjoinedFriends/{id}', 'ActivityController@inviteUnjoinedFriends');
    Route::post('inviteFriendToActivity', 'ActivityController@inviteFriendToActivity');
    Route::get('inviteAllFriendsToActivity/{id}', 'ActivityController@inviteAllFriendsToActivity');
    Route::get('mostViewedActivities', 'ActivityController@mostViewedActivities');
    Route::get('mostJoinedActivities', 'ActivityController@mostJoinedActivities');
    Route::get('replicateActivity/{id}', 'ActivityController@replicateActivity');
    Route::get('categoryImageFromSubcategory/{id}', 'ActivityController@categoryImageFromSubcategory');
    Route::post('joinActivity', 'ActivityController@joinActivity');
    Route::delete('unJoinActivity/{id}', 'ActivityController@unJoinActivity');
    Route::get('latestActivitiesByCompany/{id}', 'ActivityController@latestActivitiesByCompany');
    Route::get('getActivitiesByCityThisWeek', 'ActivityController@getActivitiesByCityThisWeek');
    Route::get('getActivitiesByCityThisMonth', 'ActivityController@getActivitiesByCityThisMonth');
    Route::get('getActivitiesOfCompaniesByCityThisMonth', 'ActivityController@getActivitiesOfCompaniesByCityThisMonth');
    Route::post('activityGallery', 'ActivityGalleryController@store');
    Route::get('activity/{id}/gallery/{size}', 'ActivityGalleryController@activityGallery');
    Route::delete('activity/{id}/gallery/{img_id}', 'ActivityGalleryController@removeImageFromGallery');
});


/**
 * ACTIVITY PLACE
 */
Route::group(['prefix' => ''], function(){
    Route::apiResource('place','PlaceController');
    Route::get('placeActivities/{id}','PlaceController@placeActivities');
    Route::get('getPlaces','PlaceController@getPlaces');




});



/**
 * FRIEND
 */
Route::group(['prefix' => ''], function (){
    Route::post('friendRequest', 'FriendshipController@userFriendRequest');
    Route::get('cancelSendFriendRequest/{id}', 'FriendshipController@cancelSendFriendRequest');
    Route::get('acceptFriendRequest/{id}', 'FriendshipController@acceptedFriendRequest');
    Route::get('denyFriendRequest/{id}', 'FriendshipController@denyFriendRequest');
    Route::get('removeFriend/{id}', 'FriendshipController@removeFriend');
    Route::post('getFriends', 'FriendshipController@getMyFriends');
    Route::get('getFriendsById/{id}', 'FriendshipController@getFriendsById');
    Route::post('getFriendRequests', 'FriendshipController@getFriendRequests');
    Route::post('isFriendWith/{id}', 'FriendshipController@isFriendWith');
    Route::post('hasFriendRequestFrom/{id}', 'FriendshipController@hasFriendRequestFrom');
    Route::post('hasSentFriendRequestTo/{id}', 'FriendshipController@hasSentFriendRequestTo');
});


/**
 * CATEGORY
 */
Route::group(['prefix' => ''], function (){
    Route::apiResource('categories', 'CategoryController');
    Route::get('subcategories', 'CategoryController@subcategories');
    Route::post('subcategory', 'CategoryController@subcategory');
    Route::get('categoriesAndSubcategories', 'CategoryController@categoriesAndSubcategories');
    Route::get('subcategoriesByCategory/{id}', 'CategoryController@subcategoriesByCategoryId');
    Route::get('addCategoryToInterests/{id}', 'CategoryController@addCategoryToInterests');
    Route::delete('removeCategoryFromInterests/{id}', 'CategoryController@removeCategoryFromInterests');
    Route::get('userInterests', 'UserController@userInterests');
    Route::get('availableCategory', 'UserController@availableCategory');
    Route::get('activitiesOfCategoryFollowing', 'CategoryController@getActivitiesOfCategoryFollowing');
    Route::get('getActivititesByCategory', 'CategoryController@getActivititesByCategory');
    Route::get('subcategoriesByCategory', 'CategoryController@subcategoriesByCategory');
});


/**
 * XP POINT
 */
Route::apiResource('xpPoint', 'UserXpPointController');
Route::post('xpAll', 'UserXpPointController@giveXpToAll');


/**
 * CHAT
 */
Route::post('message', 'ChatController@store');
Route::get('friendMessage/{id}', 'ChatController@getFriendMessage');
Route::get('lastFriendMessage', 'ChatController@getLastFriendMessage');


/**
 * SEARCH
 */
Route::post('search', 'SearchController@getUserSearch');
Route::post('autocomplete', 'SearchController@autocomplete');
Route::get('activitiesByCity/{id}', 'SearchController@getActivitiesByCity');
Route::get('switzerlandCities', 'SearchController@getSwitzerlandCities');
Route::get('getActivitiesByCityThisWeek/{id}', 'SearchController@getActivitiesByCityThisWeek');
Route::get('getActivitiesByCityThisMonth/{id}', 'SearchController@getActivitiesByCityThisMonth');
Route::get('activitiesByCategory/{id}', 'SearchController@getActivitiesByCategory');
Route::get('getActivitiesByDate/{date}', 'SearchController@getActivitiesByDate');
Route::post('searchFilters', 'SearchController@searchFilters');


/**
 * RATING
 */
Route::post('storeRating', 'RatingController@store');
Route::get('getRating/{id}', 'RatingController@getRating');
Route::get('getAverageRatingsForUser/{id}', 'UserController@getAverageRatingsForUser');
Route::get('findNrRaters/{id}', 'UserController@findNrRaters');


/**
 * FEEDBACK
 */
Route::apiResource('feedback', 'FeedbackController');


/**
 * INFO
 */
Route::get('initialInfo', 'CategoryController@initialInfo');


/**
 * Notification
 */
Route::get('notifications', 'Notification\NotificationController@notifications');
Route::delete('removeNotification/{id}', 'Notification\NotificationController@removeNotification');
Route::get('countUnReadNotifications', 'Notification\NotificationController@countUnReadNotifications');
Route::get('makeReadNotifications', 'Notification\NotificationController@makeReadNotifications');


