$(document).ready(function(){
    var el = document.querySelector('#userC');
    od = new Odometer({
        el: el,
        value: 0,

        // Any option (other than auto and selector) can be passed in here
        format: '',
        theme: 'default'
    });
    var el2 = document.querySelector('#repoC');
    od2 = new Odometer({
        el: el2,
        value: 0,
        format: '',
        theme: 'default'
    });
    var el3 = document.querySelector('#eventC');
    od3 = new Odometer({
        el: el3,
        value: 0,
        format: '',
        theme: 'default'
    });
    var el4 = document.querySelector('#relC');
    od4 = new Odometer({
        el: el4,
        value: 0,
        format: '',
        theme: 'default'
    });
    $.get("/github-users-count")
        .success(function(data){
            var elV = data.count;
            setTimeout(function(){
                od.update(elV);
            }, 500);
        });
    $.get("/github-repos-count")
        .success(function(data){
            var elv2 = data.count;
            setTimeout(function(){
                od2.update(elv2);
            }, 500);
        });
    $.get("/github-events-count")
        .success(function(data){
            var elv3 = data.count;
            setTimeout(function(){
                od3.update(elv3);
            }, 500);
        });
    $.get("/github-rels-count")
        .success(function(data){
            var elv4 = data.count;
            setTimeout(function(){
                od4.update(elv4);
            }, 500);
        });
});