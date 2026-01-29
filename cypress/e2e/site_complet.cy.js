describe('Test complet du site Vite Gourmand', () => {
  const baseUrl = 'http://127.0.0.1:8080/'; 

  it('Parcours toutes les pages et vérifie le contenu', () => {
    // Accueil
    cy.visit(baseUrl);
    cy.contains('Bienvenue'); // Vérifie un texte clé sur la home

    // Page Menu
    cy.get('a[href="/menus"]').click();
    cy.url().should('include', '/menus');
    cy.get('.menu-item').should('have.length.greaterThan', 0); // au moins un menu affiché

    // Vérifier un menu spécifique
    cy.contains('Symphonie Maritime').should('be.visible');

    // Page Contact
    cy.get('a[href="/contact"]').click();
    cy.url().should('include', '/contact');
    cy.get('form').should('exist');

    // Tester le formulaire (facultatif)
    cy.get('input[name="nom"]').type('Alice');
    cy.get('input[name="email"]').type('alice@example.com');
    cy.get('textarea[name="message"]').type('Test Cypress');
    cy.get('button[type="submit"]').click();

    cy.contains('Merci').should('be.visible'); // message de confirmation
  });
});