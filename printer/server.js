var express = require('express');
var app = express();

// respond with "hello world" when a GET request is made to the homepage
app.post('/', function(req, res) {
    if (req.query.body) {
        console.log(req.query.body);        
    } else {
        console.error('Invalid receipt.');
    }

    res.sendStatus(200);
});

app.listen(3000);
