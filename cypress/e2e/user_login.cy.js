describe('Test login utilisateur', () => {
  it('Permet de se connecter', () => {
    cy.visit('/login', { failOnStatusCode: false });
    cy.get('#email').type('ron.weasley@exemple.com');
    cy.get('#password').type('mdp123');
    cy.get('button[type="submit"]').click();

    cy.contains('Bienvenue'); // ou le texte de ta page d’accueil
  });
});