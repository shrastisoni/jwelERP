app.factory('Api', function($http) {
    return {
        setToken: function(token) {
            $http.defaults.headers.common['Authorization'] = 'Bearer ' + token;
        },
        post: (url,data)=>$http.post('/api/'+url,data),
        get: (url)=>$http.get('/api/'+url)
    };
});
