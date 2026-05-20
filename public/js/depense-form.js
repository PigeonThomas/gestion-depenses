document.addEventListener('DOMContentLoaded', () => {
    // On cherche le champ de catégorie dans le formulaire
    const categoryField = document.querySelector('[data-depense-categorie="true"]');

    // Si le champ de catégorie n'est pas trouvé, on ne fait rien
    if (!categoryField) {
        return;
    }

    // On sélectionne les différentes sections du formulaire qui doivent être affichées ou cachées
    const commonVehicleSections = document.querySelectorAll('[data-depense-section="vehicule-common"]');
    const fuelSections = document.querySelectorAll('[data-depense-section="carburant"]');
    const repairSections = document.querySelectorAll('[data-depense-section="vehicule-reparation"]');

    // Fonction pour normaliser les valeurs de texte (enlever les accents, convertir en minuscules, etc.)
    const normalizeValue = (value) => value
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .trim()
        .toLowerCase();

    // Fonction pour afficher ou cacher les sections du formulaire en fonction de la catégorie sélectionnée
    const toggleSections = (sections, isVisible) => {
        sections.forEach((section) => {
            section.hidden = !isVisible; // On cache la section si isVisible est false, sinon on l'affiche
        });
    };

    // Fonction pour mettre à jour la visibilité des sections du formulaire en fonction de la catégorie sélectionnée
    const updateVisibleFields = () => {
        const selectedOption = categoryField.options[categoryField.selectedIndex]; // On récupère l'option sélectionnée dans le champ de catégorie
        const categoryLabel = selectedOption ? normalizeValue(selectedOption.textContent) : ''; // On normalise le texte de l'option sélectionnée pour faciliter les comparaisons
        const isVehicleCategory = categoryLabel === 'vehicule'; // On vérifie si la catégorie sélectionnée est "véhicule"
        const isFuelCategory = categoryLabel === 'carburant'; // On vérifie si la catégorie sélectionnée est "carburant"
        const needsVehicleFields = isVehicleCategory || isFuelCategory; // Les champs communs au véhicule sont nécessaires pour les catégories "véhicule" et "carburant"

        toggleSections(commonVehicleSections, needsVehicleFields);  // On affiche les champs communs au véhicule si la catégorie est "véhicule" ou "carburant", sinon on les cache
        toggleSections(fuelSections, isFuelCategory); // On affiche les champs spécifiques au carburant si la catégorie est "carburant", sinon on les cache
        toggleSections(repairSections, isVehicleCategory); // On affiche les champs spécifiques à la réparation si la catégorie est "véhicule", sinon on les cache
    };

    categoryField.addEventListener('change', updateVisibleFields); // On écoute l'événement de changement sur le champ de catégorie pour mettre à jour les sections du formulaire en conséquence
    updateVisibleFields(); // On appelle la fonction une première fois pour définir l'état initial du formulaire en fonction de la catégorie sélectionnée
});
