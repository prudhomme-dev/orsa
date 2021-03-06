window.getCitiesFromZipCode = async function (zipcode, elements) {
    let selectinput = document.getElementById(elements);
    selectinput.style.display = "block";
    selectinput.options.length = 0;
    if (zipcode) {
        try {
            let response = await fetch(
                `${window.location.origin}/city/${zipcode}/`
            );
            let cities = await response.json();
            for (let city of cities) {

                let option = document.createElement('option');
                option.value = city.id;
                option.textContent = city.city;

                selectinput.appendChild(option);
            }
            selectinput.focus();
        } catch (e) {
            console.error("ERREUR", e);
        }
    }
}

window.setCity = function (idCity, elements) {
    if (idCity !== "") {
        let hiddenInput = document.getElementById(elements);
        if (hiddenInput) {
            hiddenInput.value = idCity;
        }
    }
}
