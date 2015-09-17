var choices = ["rock", "spock", "paper", "lizard", "scissors"];
var map = {};

choices.forEach(function(choice, i) {
    map[choice] = {};
    for (var j = 0, half = (choices.length-1)/2; j < choices.length; j++) {
        var opposition = (i+j)%choices.length
        if (!j)
            map[choice][choice] = "Was a tie"
        else if (j <= half)
            map[choice][choices[opposition]] = choices[opposition] + " wins"
        else
            map[choice][choices[opposition]] = choice + " wins"
    }
})

function compare(choice1, choice2) {
    return (map[choice1] || {})[choice2] || "Invalid choice";
}