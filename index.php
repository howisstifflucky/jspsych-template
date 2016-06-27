<!doctype html>
<html>
  <head>
    <title>Study</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <!-- Make sure to define the correct location for jspsych below. -->
    <script src="../jspsych/jspsych.js"></script>
    <script src="../jspsych/plugins/jspsych-html.js"></script>
    <script src="../jspsych/plugins/jspsych-single-stim.js"></script>
    <script src="../jspsych/plugins/jspsych-survey-likert.js"></script>
    <script src="../jspsych/plugins/jspsych-survey-text.js"></script>
    <script src="../jspsych/plugins/jspsych-text.js"></script>
    <link href="../jspsych/css/jspsych.css" rel="stylesheet" type="text/css"></link>
    <style>
      .parent-centered {
        position: relative;
      }
      .centered {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 32px;
        text-align: center;
      }
    </style>
  </head>
  <body>
  </body>
  <script>
  // Used to pass data to save_data.php
    function saveData(filename, filedata){
     $.ajax({
        type:'post',
        cache: false,
        url: 'save_data.php', // this is the path to the above PHP script
        data: {filename: filename, filedata: filedata}
     });
  }

  // Generates a subject id, and adds it to the data.
  // Make sure there is enough entropy with subject number generation.
  var subject_id = Math.floor(Math.random()*100000);
    jsPsych.data.addProperties({
    subject: subject_id
  });

  // Checks if participants have given consent to take part in the study.
  var check_consent = function(elem) {
    if ($('#consent_checkbox').is(':checked')) {
      return true;
    } else {
      alert("If you wish to participate, you must check the box next to the" + 
        " statement 'I agree to participate in this study.'");
      return false;
    }
    return false;
  };
  // The path to the consent document.
  var info = {
    type:'html',
    url: "consent.html",
    cont_btn: "start",
    check_fn: check_consent
  };
  // Study welcome text.
  var welcome = {
    type: "text",
    text: "Welcome to the study. Press any key to begin."
  };
  // Get some basic demographic information.
  var demographics = {
    type: 'survey-text',
    preamble: 'Please provide us with some demographic information.',
    questions: ["Age", "Sex", "Educational Level", "Employment Status"]
  };
  // Participant instructions.
  var instructions = {
    type: "text",
    text: '<p>In this task, you will be required to press some keys.' +
          'You are tasked with pressing either <b>Z</b> or <b>M</b> if the ' +
          'depending upon the second letter you see. For example: press Z if ' +
          'the letter is X, M if the letter is Y.</p>' +
          '<p>This will be explained at the start of each block of trials</p>' +
          '<p>Press any key to begin.</p>',
    timing_post_trial: 2000
  };
  // Trials for block A & B. Replace and rename as needed.
  var blockATrials = [
    {let1: '<div class="centered">X</div>', let2: '<div class="centered">Y</div>', correct: "90"},
    {let1: '<div class="centered">Y</div>', let2: '<div class="centered">X</div>', correct: "77"},
  ];

  var blockBTrials = [
    {let1: '<div class="centered">X</div>', let2: '<div class="centered">Y</div>', correct: "77"},
    {let1: '<div class="centered">Y</div>', let2: '<div class="centered">X</div>', correct: "90"},
  ];

  // Define score and trial variables for later recursion.
  var score = 0
  var trial = 0

  // Define the rule for Block A
  var first_rule = {
    type: 'text',
    text: '<p>Should you need to take a break, please do so now.</p>' +
    '<p>In the trials that follow, press <b>Z</b> if the second letter you see ' + 
    'is Y, press <b>M</b> if the second letter you see is X.</p>',
  };

  var blockA = {
    timeline: []
  };
  for (var i in blockATrials) {
    blockA.timeline.push({
        type: 'single-stim',
        stimulus: ['<div class="centered">+</div>'],
        is_html: true,
        timing_stim: 500,
        timing_response: 500,
        choices: 'none'
    });
    blockA.timeline.push({
        type: 'single-stim',
        stimulus: blockATrials[i].let1,
        is_html: true,
        timing_stim: 1000,
        timing_response: 1000,
        choices: 'none'
    });
    blockA.timeline.push({
        type: 'single-stim',
        stimulus: ['<div class="centered">+</div>'],
        is_html: true,
        timing_stim: 500,
        timing_response: 500,
        choices: 'none'
    });
    blockA.timeline.push({
        type: 'single-stim',
        stimulus: blockATrials[i].let2, 
        is_html: true,
        timing_stim: 2000,
        timing_response: 2000, // Allows 2 seconds for response.
        choices: ['z', 'm'],
        on_finish: function(trial_data) {
          // Add the raw keypress as a column. Add the correct as a column.
          var keyPress = trial_data.key_press;
          var correct = blockATrials[i].correct;
          // Checks if the keypress matches the correct response.
          var isCorrect = (trial_data.key_press == blockATrials[i].correct);
          // Increase trial count
          trial = trial + 1;
          // If correct, increase score count.
          if (trial_data.key_press == blockBTrials[i].correct)
          {
            score = score + 1;
          }
          jsPsych.data.addDataToLastTrial({keyPress: keyPress, correct: correct, isCorrect: isCorrect});
        }
    });
  }    

  // Second 128 trials
  var second_rule = {
    type: 'text',
    text: '<p>Should you need to take a break, please do so now.</p>' +
    '<p>In the trials that follow, press <b>Z</b> if the second letter you see ' + 
    'is X, press <b>M</b> if the second letter you see is Y.</p>',
  };

  var blockB = {
    timeline: []
  };
  for (var i in blockBTrials) {
    blockB.timeline.push({
        type: 'single-stim',
        stimulus: ['<div class="centered">+</div>'],
        is_html: true,
        timing_stim: 500,
        timing_response: 500,
        choices: 'none'
    });
    blockB.timeline.push({
        type: 'single-stim',
        stimulus: blockBTrials[i].let1,
        is_html: true,
        timing_stim: 1000,
        timing_response: 1000,
        choices: 'none'
    });
    blockB.timeline.push({
        type: 'single-stim',
        stimulus: ['<div class="centered">+</div>'],
        is_html: true,
        timing_stim: 500,
        timing_response: 500,
        choices: 'none'
    });
    blockB.timeline.push({
        type: 'single-stim',
        stimulus: blockBTrials[i].let2, 
        is_html: true,
        timing_response: 2000,
        timing_response: 2000, // Allows 2 seconds for response
        choices: ['z', 'm'],
        on_finish: function(trial_data) {
          // Add the raw keypress as a column. Add the correct as a column.
          var keyPress = trial_data.key_press;
          var correct = blockBTrials[i].correct;
          // Checks if the keypress matches the correct response.
          var isCorrect = (trial_data.key_press == blockBTrials[i].correct);
          // Increase trial count
          trial = trial + 1;
          // If correct, increase score count.
          if (trial_data.key_press == blockBTrials[i].correct)
          {
            score = score + 1
          }
          jsPsych.data.addDataToLastTrial({keyPress: keyPress, correct: correct, isCorrect: isCorrect});
        }
    });
  }

  var debrief = {
    type: 'text',
    text: '<p><b>PARTICIPANT DEBRIEF</b></p>' +
    //Display participant's unique ID
    '<p><b>Your subject id is: </b>' + subject_id +
    '</p><p>Please make a note of this, should you need to contact the the ' +
    'experimental team.</p>' +
    '<p><b>Name of researcher:</b> blabla </p>' +
    '<p><b>Name of supervisor:</b> albalb</p>' +
    '<p><b>Contact e-mail:</b> blabla[at]university.alb</p>' +
    '<p><b>Project title:</b> Something or other ' +
    '<p><b>What was the purpose of the project?</b></p>' +
    '<p>To do something.</p> <p>We expect to find something.</p>' +
    '<p><b>How can I find out about my results?</b></p>' +
    "<p>You can't. Go away</p>" +
    '<p><b>Have I been decieved in any way during the project?</b>' +
    '<p>Yes</p><p><b>If I change my mind about participation and wish to ' +
    'withdraw the information I have provided, how do I do this?</b></p>' +
    '<p>Tough.</p> ',
  };

  // Define the structure
  var timeline = [];
  timeline.push(info);
  timeline.push(welcome);
  timeline.push(demographics);
  timeline.push(instructions);
  if (Math.round(Math.random() * 10) % 2 == 0)
  {
    timeline.push(first_rule);
    timeline.push(blockA);
    timeline.push(second_rule);
    timeline.push(blockB);
  }
  else
  {
    timeline.push(second_rule);
    timeline.push(blockB);
    timeline.push(first_rule);
    timeline.push(blockA);
  }
  timeline.push(debrief);

  /* start the experiment */
  jsPsych.init({
    timeline: timeline,
    on_finish: function(data) {
      saveData(subject_id + '.csv', jsPsych.data.dataAsCSV());
    }
  });

</script>
</html>