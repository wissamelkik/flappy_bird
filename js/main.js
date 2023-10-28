window.password = "";

// jCryption
function jAjax(request_string, success_callback, failure_callback){
	// Init jCryption Authentication
	window.password = $.jCryption.encrypt("XXXX", "trubo");
	$.jCryption.authenticate(
		window.password, 
		"action/jcryption.php?gpk=true", 
		"action/jcryption.php?hs=true",
		function(AESKey){
			console.log("func 1");
			// Authenticated
			var request_encrypted_str = $.jCryption.encrypt(request_string, window.password);
			$.ajax({
				url: "action/jcryption.php",
				dataType: "json",
				type: "POST",
				data: {
					gd: request_encrypted_str
				}
			});
		}, 
		function(){
			console.log("func 2");
			// Authentication failed
			//failure_callback();
		}
	);
}

// Initialize Phaser, and creates a 400x490px game
window.game = new Phaser.Game(400, 490, Phaser.AUTO, 'game_div');

// States
window.menu_state = {

    preload: function() {
		// Change the background color of the game
		this.game.stage.backgroundColor = '#ddd';
		// Load the images
		this.game.load.image('start_btn', 'images/start_btn.png'); 
		this.game.load.image('background', 'images/bg.jpg');
		this.game.load.image('logo', 'images/logo.png');
		// Variables
		this.game_started = false;
    },

    create: function() {
		// Display the background
		this.bg = this.game.add.sprite(0, 0, 'background');
		this.bg.body.immovable = true;
		this.bg.fixedToCamera = true;
		// Display the logo
		this.logo = this.game.add.sprite(50, 100, 'logo');
		this.logo.body.immovable = true;
		this.logo.fixedToCamera = true;
		// Display the start button
		this.start_button = this.game.add.button(this.game.width/2, 300, 'start_btn', this.start_game, this);
		this.start_button.anchor.setTo(0.5,0.5);

    },
	
	start_game: function() {
		this.game.state.start('login');
	}
};

window.start_state = {

    preload: function() {
		// Change the background color of the game
		this.game.stage.backgroundColor = '#ddd';
		// Load the images
		this.game.load.image('bird', 'images/bird.png');
		this.game.load.image('pipe', 'images/pipe.png'); 
		this.game.load.image('start_btn', 'images/start_btn.png'); 
		this.game.load.image('background', 'images/bg.jpg');
		this.game.load.image('menu_btn', 'images/menu_btn.png');
		// Variables
		this.game_started = false;
    },

    create: function() {
		// Display the background
		this.bg = this.game.add.sprite(0, 0, 'background');
		this.bg.body.immovable = true;
		this.bg.fixedToCamera = true;
		// Display the menu button
		this.menu_button = this.game.add.button(this.game.width-20, 20, 'menu_btn', this.open_menu, this);
		this.menu_button.anchor.setTo(0.5,0.5);
		// Display the bird
		this.bird = this.game.add.sprite(100, 245, 'bird');
		// Add the pipes to the group of pipes
		this.pipes = game.add.group();
		this.pipes.createMultiple(20, 'pipe');
		// Assign a function when the spacekey is hit
		var space_key = this.game.input.keyboard.addKey(Phaser.Keyboard.SPACEBAR);
		space_key.onDown.add(this.handle_spacebar, this);
		// Set the score
		this.score = 0;  
		var style = { font: "30px Arial", fill: "#ffffff" };  
		this.label_score = this.game.add.text(20, 20, "0", style);
		this.label_score.content = this.score;
		// Hint message
		var style = { font: "14px Arial", fill: "#ffffff", align: "center" };  
		this.label_hint = this.game.add.text(100, 20, "0", style);
		this.label_hint.content = "[ Click on the spacebar to start ]";
    },
    
    update: function() {
		// If the bird is out of the world
		if(this.bird.inWorld == false){
			this.end_game();
		}
		// Collision between the bird and the pipes
		this.game.physics.overlap(this.bird, this.pipes, this.end_game, null, this);
    },
	
	open_menu: function() {
		// Kill the timer
		this.game.time.events.remove(this.timer);
		// End the game
		this.game.state.start('menu');
    },
	
	handle_spacebar: function(){
		if(this.game_started == false){
			// Set the variables
			this.game_started = true;
			// Hide the hint message
			this.label_hint.visible = false;
			// Add gravity to the bird
			this.bird.body.gravity.y = 1000;
			// Set the timer
			this.timer = this.game.time.events.loop(1500, this.add_row_of_pipes, this);
		} else {
			// Call the 'jump' function when the spacekey is hit
			this.jump();
		}
	},
	
	// Make the bird jump 
	jump: function() {  
		// Add a vertical velocity to the bird
		this.bird.body.velocity.y = -350;
	},
	
	// End the game
	end_game: function(){
		// Kill the timer
		this.game.time.events.remove(this.timer);
		// Save the score
		localStorage.setItem('lastScore', this.score);
		// End the game
		this.game.state.start('end');
	},
	
	add_one_pipe: function(x, y) {
		// Get the first dead pipe of our group
		var pipe = this.pipes.getFirstDead();
		// Set the new position of the pipe
		pipe.reset(x, y);
		// Add velocity to the pipe to make it move left
		pipe.body.velocity.x = -200;
		// Kill the pipe when it's no longer visible 
		pipe.outOfBoundsKill = true;
	},
	
	add_row_of_pipes: function(){
		// Add one more pipe
		var hole = Math.floor(Math.random()*5)+1;
		for (var i = 0; i < 8; i++){
			if (i != hole && i != hole +1){
				this.add_one_pipe(400, i*60+10);   
			}
		}
		// Update the score
		this.score += 1; 
		this.label_score.content = this.score; 
	},
};

var end_state = {

    preload: function() {
		// Change the background color of the game
		this.game.stage.backgroundColor = '#ddd';
		// Load the images
		this.game.load.image('restart_btn', 'images/restart_btn.png'); 
		this.game.load.image('background', 'images/bg.jpg');
		this.game.load.image('menu_btn', 'images/menu_btn.png');
    },

    create: function() {
		// Display the background
		this.bg = this.game.add.sprite(0, 0, 'background');
		this.bg.body.immovable = true;
		this.bg.fixedToCamera = true;
		// Display the menu button
		this.menu_button = this.game.add.button(this.game.width-20, 20, 'menu_btn', this.open_menu, this);
		this.menu_button.anchor.setTo(0.5,0.5);
		// Display the restart button
		this.restart_btn = this.game.add.button(this.game.width/2, 300, 'restart_btn', this.restart_game, this);
		this.restart_btn.anchor.setTo(0.5,0.5);
		// Messages
		var style = { font: "30px Arial", fill: "#ffffff", align: "center" };  
		this.label_game_end = this.game.add.text(60, 100, "0", style);
		// Score
		this.username = localStorage.getItem('username');
		this.last_score = localStorage.getItem('lastScore');
		this.best_score = localStorage.getItem('bestScore');
		if(this.best_score == 0){
			localStorage.setItem('bestScore', this.last_score);
			this.label_game_end.content = "Congrats " + this.username + "!\n\n Your score is: " + this.last_score;
		} else if(this.last_score > this.best_score){
			localStorage.setItem('bestScore', this.last_score);
			this.label_game_end.content = "Congrats " + this.username + "!\n\n You just improved \n your score: " + this.last_score;
		} else {
			this.label_game_end.content = "Hard Luck " + this.username + "!\n\n Your score is: " + this.last_score + "\n Your best score is: " + this.best_score;
		}
		// Save the score in the database
		if(typeof this.username != "undefined" && this.username.length > 0 && typeof this.last_score != "undefined" && this.last_score.length > 0){
			var that = this;
			this.request_str = "ss" + window.sp + this.username + window.sp + window.hash + window.sp + window.fingerprint + window.sp + this.last_score;
			jAjax(this.request_str, null, function(){
				displayError(that);
			});
		} else {
			displayError(this);
		}
		// Display the restart button
		this.restart_button = this.game.add.button(this.game.width/2, 300, 'restart_btn', this.restart_game, this);
		this.restart_button.anchor.setTo(0.5,0.5);
    },
	
	open_menu: function() {
		// Kill the timer
		this.game.time.events.remove(this.timer);
		// End the game
		this.game.state.start('menu');
    },

	// Restart the game
	restart_game: function(){
		this.game.state.start('start');
	}
};

window.login_state = {

    preload: function() {
		// Change the background color of the game
		this.game.stage.backgroundColor = '#ddd';
		// Load the images
		this.game.load.image('go_btn', 'images/go_btn.png'); 
		this.game.load.image('background', 'images/bg.jpg');
		this.game.load.image('menu_btn', 'images/menu_btn.png');
    },

    create: function() {
		// Display the background
		this.bg = this.game.add.sprite(0, 0, 'background');
		this.bg.body.immovable = true;
		this.bg.fixedToCamera = true;
		// Display the menu button
		this.menu_button = this.game.add.button(this.game.width-20, 20, 'menu_btn', this.open_menu, this);
		this.menu_button.anchor.setTo(0.5,0.5);
		// Display the form
		$("#game_form").show();
		// Display the start button
		this.go_button = this.game.add.button(this.game.width/2, 300, 'go_btn', this.start_game, this);
		this.go_button.anchor.setTo(0.5,0.5);
    },

	// Start the game
	start_game: function(){
		var username = $("#formUsername").val();
		if(username.length > 0){
			// Clear the localStorage
			localStorage.setItem('username', '');
			localStorage.setItem('bestScore', '');
			localStorage.setItem('lastScore', '');
			// Add the new username to the database
			var that = this;
			this.request_str = "hu" + window.sp + this.username + window.sp + window.hash + window.sp + window.fingerprint + window.sp + this.last_score;
			jAjax(this.request_str, function(response){
				if(typeof response != "undefined"){
					// Refresh the data in the localStorage
					localStorage.setItem('username', username);
					localStorage.setItem('bestScore', response.user_best_score);
					localStorage.setItem('lastScore', response.user_last_score);
				} else {
					displayError(that);
				}
			}, function(){
				displayError(that);
			});
			// Hide the form
			$("#game_form").hide();
			// Start the game
			this.game.state.start('start');
		}
	}
};

// Add and start the 'main' state to start the game
game.state.add('start', start_state);  
game.state.add('end', end_state);
game.state.add('menu', menu_state);
game.state.add('login', login_state);
game.state.start('menu');

// Function to display an error in the game
function displayError(gameObj){
	var style = { font: "14px Arial", fill: "#ffffff", align: "center" };  
	gameObj.label_error = gameObj.game.add.text(150, 20, "0", style);
	gameObj.label_error.content = "An Error Occured";
}