// Heti ajánlatok visszaszámláló script
const weeklyDealContainer = document.createElement('div');
weeklyDealContainer.id = 'weekly-deal';
weeklyDealContainer.style.cssText = `
    background: linear-gradient(90deg, #ffe3d3, #ffbdbd);
    color: #333;
    text-align: center;
    padding: 3em 1em;
    border-radius: 15px;
    margin: 2em auto;
    max-width: 600px;
    box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.2);
`;

const dealHeading = document.createElement('h2');
dealHeading.textContent = 'Heti ajánlat: Minden pizza féláron!';
dealHeading.style.cssText = `
    font-size: 2em;
    font-weight: bold;
    margin-bottom: 1em;
    color: #ff5733;
`;

const countdownDisplay = document.createElement('div');
countdownDisplay.id = 'countdown';
countdownDisplay.style.cssText = `
    font-size: 2.5em;
    font-weight: bold;
    color: #ff4500;
    animation: pulse 1.5s infinite;
`;

weeklyDealContainer.appendChild(dealHeading);
weeklyDealContainer.appendChild(countdownDisplay);
document.body.prepend(weeklyDealContainer);

// Visszaszámláló logika
function updateCountdown() {
    const now = new Date();
    const endOfWeek = new Date(now.getFullYear(), now.getMonth(), now.getDate() + (7 - now.getDay()), 23, 59, 59);
    const diff = endOfWeek - now;

    if (diff <= 0) {
        countdownDisplay.textContent = 'Az ajánlat lejárt!';
        countdownDisplay.style.color = '#999';
        return;
    }

    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    countdownDisplay.textContent = `${days} nap ${hours} óra ${minutes} perc ${seconds} másodperc`; 
}

setInterval(updateCountdown, 1000);
updateCountdown();
