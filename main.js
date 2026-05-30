document.addEventListener("DOMContentLoaded", function () {
    var knownCities = Array.isArray(window.voyageVistaCities) && window.voyageVistaCities.length ? window.voyageVistaCities : [
        "Paris", "Lyon", "Marseille", "Bordeaux", "Lille", "Nice", "Nantes", "Toulouse",
        "Lisbonne", "Marrakech", "Santorin", "Bali", "Denpasar", "Kyoto", "Chamonix", "Genève", "Athènes", "Rome",
        "Barcelone", "Amsterdam", "Londres", "New York", "Tokyo"
    ];

    var savedTheme = localStorage.getItem("voyagevista_theme");
    var applyTheme = function (theme) {
        var isDark = theme === "dark";
        document.documentElement.classList.toggle("dark-mode", isDark);
        document.body.classList.toggle("dark-mode", isDark);
        document.querySelectorAll("[data-theme-toggle]").forEach(function (button) {
            var label = button.querySelector("span");
            if (label) label.textContent = isDark ? "Mode clair" : "Mode sombre";
            button.setAttribute("aria-label", isDark ? "Passer en mode clair" : "Passer en mode sombre");
        });
    };

    applyTheme(savedTheme === "dark" ? "dark" : "light");

    document.querySelectorAll("[data-theme-toggle]").forEach(function (button) {
        button.addEventListener("click", function () {
            var nextTheme = document.body.classList.contains("dark-mode") ? "light" : "dark";
            localStorage.setItem("voyagevista_theme", nextTheme);
            applyTheme(nextTheme);
        });
    });

    var menuButton = document.querySelector("[data-menu-toggle]");
    var mainNav = document.querySelector("[data-main-nav]");

    if (menuButton && mainNav) {
        menuButton.addEventListener("click", function () {
            mainNav.classList.toggle("open");
        });
    }

    function normalize(value) {
        return value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    document.querySelectorAll("[data-city-autocomplete]").forEach(function (input) {
        input.addEventListener("input", function (event) {
            var value = input.value.trim();
            if (event.inputType && event.inputType.indexOf("delete") === 0) return;
            if (value.length < 3) return;

            var match = knownCities.find(function (city) {
                return normalize(city).indexOf(normalize(value)) === 0 && normalize(city) !== normalize(value);
            });

            if (match) {
                input.value = match;
                input.setSelectionRange(value.length, match.length);
            }
        });
    });

    function bindOutput(selector, formatter) {
        document.querySelectorAll(selector).forEach(function (range) {
            var output = document.getElementById(range.getAttribute(selector.replace("[", "").replace("]", "")));
            var updateOutput = function () {
                if (output) output.textContent = formatter(range.value);
            };
            range.addEventListener("input", updateOutput);
            updateOutput();
        });
    }

    document.querySelectorAll("[data-range-output]").forEach(function (range) {
        var output = document.getElementById(range.getAttribute("data-range-output"));
        var updateOutput = function () {
            if (output) {
                output.textContent = new Intl.NumberFormat("fr-FR", {
                    style: "currency",
                    currency: "EUR",
                    maximumFractionDigits: 0
                }).format(Number(range.value));
            }
        };
        range.addEventListener("input", updateOutput);
        updateOutput();
    });

    bindOutput("[data-days-output]", function (value) { return value + " jours"; });
    bindOutput("[data-hour-output]", function (value) { return String(value).padStart(2, "0") + ":00"; });
    bindOutput("[data-rooms-output]", function (value) { return value + " chambre(s)"; });
    bindOutput("[data-km-output]", function (value) { return value + " km max"; });

    var serviceForm = document.querySelector("[data-service-form]");
    if (serviceForm) {
        var serviceRules = {
            vols: ["departure", "arrival", "date_depart", "date_retour", "trip_type", "adults", "children"],
            hebergements: ["arrival", "date_depart", "date_retour", "adults", "children", "rooms"],
            voitures: ["departure", "arrival", "date_depart", "date_retour"],
            vacances: ["departure", "arrival", "date_depart", "date_retour", "trip_type", "persons", "rooms"],
            activites: ["arrival", "date_depart", "adults", "children"]
        };

        var fieldLabels = {
            vols: ["Point de départ", "Point d'arrivée"],
            hebergements: ["", "Destination"],
            voitures: ["Prise en charge", "Dépôt ailleurs"],
            vacances: ["Point de départ", "Destination"],
            activites: ["", "Destination"]
        };

        var applyService = function () {
            var checked = serviceForm.querySelector("input[name='service']:checked");
            var service = checked ? checked.value : "vols";
            var visibleFields = serviceRules[service] || serviceRules.vols;
            var labels = fieldLabels[service] || fieldLabels.vols;

            serviceForm.querySelectorAll("[data-field]").forEach(function (field) {
                field.classList.toggle("is-hidden", !visibleFields.includes(field.getAttribute("data-field")));
            });

            var departureLabel = serviceForm.querySelector("[data-label-depart]");
            var arrivalLabel = serviceForm.querySelector("[data-label-arrival]");
            if (departureLabel && labels[0]) departureLabel.textContent = labels[0];
            if (arrivalLabel && labels[1]) arrivalLabel.textContent = labels[1];
        };

        serviceForm.querySelectorAll("input[name='service']").forEach(function (radio) {
            radio.addEventListener("change", applyService);
        });
        applyService();
    }

    function getFavorites() {
        try {
            return JSON.parse(localStorage.getItem("voyagevista_favorites")) || [];
        } catch (error) {
            return [];
        }
    }

    function saveFavorites(favorites) {
        localStorage.setItem("voyagevista_favorites", JSON.stringify(favorites));
    }

    function isLoggedIn() {
        return localStorage.getItem("voyagevista_logged_in") === "1";
    }

    function showToast(message) {
        var zone = document.querySelector("[data-toast-zone]");
        if (!zone) return;

        var toast = document.createElement("div");
        toast.className = "toast";
        toast.textContent = message;
        zone.appendChild(toast);

        setTimeout(function () {
            toast.remove();
        }, 3500);
    }

    window.voyageVistaToast = showToast;

    document.querySelectorAll("[data-auth-required]").forEach(function (section) {
        if (isLoggedIn()) {
            section.classList.add("is-authenticated");
        } else {
            section.classList.remove("is-authenticated");
        }
    });

    document.querySelectorAll("[data-favorite]").forEach(function (button) {
        button.addEventListener("click", function () {
            var name = button.getAttribute("data-favorite");
            var favorites = getFavorites();

            if (!favorites.includes(name)) {
                favorites.push(name);
                saveFavorites(favorites);
                button.textContent = "Favori ajouté";
                showToast("Destination ajoutée aux favoris.");
            } else {
                showToast("Cette destination est déjà dans les favoris.");
            }
        });
    });

    function renderFavorites() {
        var list = document.querySelector("[data-favorites-list]");
        var empty = document.querySelector("[data-favorites-empty]");
        if (!list) return;

        var favorites = getFavorites();
        list.innerHTML = "";

        if (empty) empty.style.display = favorites.length ? "none" : "block";

        favorites.forEach(function (name) {
            var item = document.createElement("article");
            item.className = "favorite-item";
            item.innerHTML = "<h2></h2><button class=\"btn btn-light\" type=\"button\">Retirer</button>";
            item.querySelector("h2").textContent = name;
            item.querySelector("button").addEventListener("click", function () {
                saveFavorites(getFavorites().filter(function (favorite) {
                    return favorite !== name;
                }));
                renderFavorites();
                showToast("Favori retiré.");
            });
            list.appendChild(item);
        });
    }

    renderFavorites();

    var clearFavorites = document.querySelector("[data-clear-favorites]");
    if (clearFavorites) {
        clearFavorites.addEventListener("click", function () {
            saveFavorites([]);
            renderFavorites();
            showToast("Liste des favoris vidée.");
        });
    }


    // Panier persistant côté navigateur : le panier reste accessible même sans connexion.
    var cartKey = "voyagevista_last_cart_url";
    var currentPage = window.location.pathname.split("/").pop();

    if (currentPage === "panier.php" && window.location.search.length > 1) {
        localStorage.setItem(cartKey, window.location.href);
    }

    if (currentPage === "panier.php" && window.location.search.length <= 1) {
        var savedCartUrl = localStorage.getItem(cartKey);
        if (savedCartUrl) {
            var restoreBox = document.querySelector("[data-restore-cart]");
            if (restoreBox) {
                restoreBox.innerHTML = '<p>Un panier sauvegardé existe sur ce navigateur.</p><a class="btn btn-primary" href="' + savedCartUrl + '">Restaurer le panier</a>';
            } else {
                window.location.href = savedCartUrl;
            }
        }
    }

    document.querySelectorAll('a[href^="panier.php?"]').forEach(function (link) {
        link.addEventListener("click", function () {
            localStorage.setItem(cartKey, new URL(link.getAttribute("href"), window.location.href).href);
        });
    });

    var paymentButton = document.querySelector("[data-payment-button]");
    if (paymentButton) {
        paymentButton.addEventListener("click", function () {
            paymentButton.disabled = true;
            paymentButton.textContent = "Confirmation en cours…";

            fetch("includes/api_panier.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "confirm" })
            })
            .then(function (response) {
                return response.text().then(function (text) {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error("Réponse invalide : " + text.substring(0, 200));
                    }
                });
            })
            .then(function (data) {
                if (data.ok) {
                    localStorage.removeItem("voyagevista_last_cart_url");
                    window.location.href = "confirmation.php";
                } else if (data.error === "Non connecté.") {
                    showToast("Connectez-vous avant de confirmer votre panier.");
                    paymentButton.disabled = false;
                    paymentButton.textContent = "Confirmer le panier";
                } else {
                    showToast("Erreur : " + (data.error || "inconnue"));
                    paymentButton.disabled = false;
                    paymentButton.textContent = "Confirmer le panier";
                }
            })
            .catch(function (err) {
                showToast(err.message || "Erreur réseau.");
                paymentButton.disabled = false;
                paymentButton.textContent = "Confirmer le panier";
            });
        });
    }

    document.querySelectorAll("[data-toast-button]").forEach(function (button) {
        button.addEventListener("click", function () {
            showToast(button.getAttribute("data-toast-button"));
        });
    });


    function formatEuro(value) {
        return new Intl.NumberFormat("fr-FR", {
            style: "currency",
            currency: "EUR",
            maximumFractionDigits: 0
        }).format(Number(value || 0));
    }

    function updateTransportLivePrices() {
        var adultsInput = document.querySelector("[data-live-adults]");
        var childrenInput = document.querySelector("[data-live-children]");
        if (!adultsInput || !childrenInput) return;

        var adults = Math.max(1, Number(adultsInput.value || 1));
        var children = Math.max(0, Number(childrenInput.value || 0));
        var persons = adults + children;

        document.querySelectorAll("[data-live-summary='adults']").forEach(function (node) { node.textContent = adults; });
        document.querySelectorAll("[data-live-summary='children']").forEach(function (node) { node.textContent = children; });
        document.querySelectorAll("[data-live-summary='persons']").forEach(function (node) { node.textContent = persons; });
        document.querySelectorAll("[data-traveler-heading]").forEach(function (node) { node.textContent = persons + " personne(s)"; });

        document.querySelectorAll("[data-fare-card]").forEach(function (form) {
            var base = Number(form.getAttribute("data-base-price") || 0);
            var bags = (form.querySelector("[data-fare-option='bags']") || {}).value || "0";
            var ticket = (form.querySelector("[data-fare-option='ticket']") || {}).value || "Basic";
            var seat = (form.querySelector("[data-fare-option='seat']") || {}).value || "Standard";

            var bagFee = bags === "soute" ? 40 : 0;
            var ticketAdultFee = ticket === "Premium" ? 80 : (ticket === "Flex" ? 35 : 0);
            var ticketChildFee = ticket === "Premium" ? 50 : (ticket === "Flex" ? 20 : 0);
            var seatFee = seat === "Espace+" ? 25 : (seat === "Fenêtre" ? 12 : (seat === "Couloir" ? 10 : 0));
            var childBase = Math.round(base * 0.70);
            var adultUnit = base + bagFee + ticketAdultFee + seatFee;
            var childUnit = childBase + bagFee + ticketChildFee + seatFee;
            var total = (adultUnit * adults) + (childUnit * children);

            var totalNode = form.querySelector("[data-fare-total]");
            var travelerNode = form.querySelector("[data-fare-travelers]");
            var breakdownNode = form.querySelector("[data-fare-breakdown]");
            if (totalNode) totalNode.textContent = formatEuro(total);
            if (travelerNode) travelerNode.textContent = adults + " adulte(s) · " + children + " enfant(s)";
            if (breakdownNode) breakdownNode.textContent = "Adulte : " + formatEuro(adultUnit) + " · Enfant : " + formatEuro(childUnit);

            var hiddenAdults = form.querySelector("input[name='adults']");
            var hiddenChildren = form.querySelector("input[name='children']");
            var hiddenPersons = form.querySelector("input[name='persons']");
            if (hiddenAdults) hiddenAdults.value = adults;
            if (hiddenChildren) hiddenChildren.value = children;
            if (hiddenPersons) hiddenPersons.value = persons;
        });
    }

    document.querySelectorAll("[data-live-adults], [data-live-children]").forEach(function (input) {
        input.addEventListener("input", updateTransportLivePrices);
        input.addEventListener("change", updateTransportLivePrices);
    });

    document.querySelectorAll("[data-fare-option]").forEach(function (input) {
        input.addEventListener("change", updateTransportLivePrices);
    });

    updateTransportLivePrices();

    var loginForm = document.querySelector("[data-login-form]");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            event.preventDefault();
            localStorage.setItem("voyagevista_logged_in", "1");
            var message = document.querySelector("[data-login-message]");
            if (message) message.textContent = "Connexion réussie.";
            showToast("Connexion réussie.");
        });
    }
});
