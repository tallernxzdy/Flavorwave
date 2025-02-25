document.addEventListener("DOMContentLoaded", function() {
    const questions = document.querySelectorAll(".quiz-question");
    const resultCard = document.querySelector(".quiz-result-card");
    const recommendedFood = document.getElementById("recommended-food");
    const foodImage = document.getElementById("food-image");
    const orderLink = document.getElementById("order-link");

    let answers = {
        type: null,
        spice: null,
        cheese: null
    };

    let currentQuestionIndex = 0;

    function showNextQuestion() {
        questions[currentQuestionIndex].classList.remove("active");
        currentQuestionIndex++;
        if (currentQuestionIndex < questions.length) {
            questions[currentQuestionIndex].classList.add("active");
        } else {
            recommendFood();
            resultCard.classList.add("show");
        }
    }

    questions.forEach((question, index) => {
        const options = question.querySelectorAll(".quiz-options button");
        options.forEach(option => {
            option.addEventListener("click", () => {
                const key = Object.keys(answers)[index];
                answers[key] = option.dataset[key];
                showNextQuestion();
            });
        });
    });

    function recommendFood() {
        const { type, spice, cheese } = answers;
        let food = "";
        let image = "";
        let link = "pizza.php?type=";

        if (type === "meat") {
            food = "Húsos Pizza";
            image = "../kepek/pizza2.jpg";
            link += "meat";
        } else if (type === "veggie") {
            food = "Zöldséges Pizza";
            image = "../kepek/pizza2.jpg";
            link += "veggie";
        } else if (type === "cheese") {
            food = "Sajtos Pizza";
            image = "../kepek/pizza2.jpg";
            link += "cheese";
        }

        if (spice === "medium") {
            food += " közepesen csípős";
            link += "&spice=medium";
        } else if (spice === "hot") {
            food += " extra csípős";
            link += "&spice=hot";
        }

        if (cheese === "extra") {
            food += " extra sajttal";
            link += "&cheese=extra";
        }

        recommendedFood.textContent = food;
        foodImage.src = image;
        orderLink.href = link;
    }
});