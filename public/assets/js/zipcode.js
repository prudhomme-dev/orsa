async function getCitiesFromZipCode(zipcode, elements) {
    let selectinput = document.getElementById(elements);

    selectinput.options.length = 0;
    try {
        let response = await fetch(
            `${window.location.origin}/json/city/${zipcode}/`
        );
        let cities = await response.json();
        for (let city of cities) {

            let option = document.createElement('option');
            option.value = city.id;
            option.textContent = city.city;

            selectinput.appendChild(option);
        }
    } catch (e) {
        console.error("ERREUR", e);
    }
}