// Script d'initialisation MongoDB

db = db.getSiblingDB('vite_gourmand_stats');

db.createCollection('menu_statistics');

db.menu_statistics.createIndex({ "menu_id": 1 });
db.menu_statistics.createIndex({ "periode": 1 });
db.menu_statistics.createIndex({ "menu_id": 1, "periode": 1 }, { unique: true });

db.createCollection('ca_global');
db.ca_global.createIndex({ "periode": 1 }, { unique: true });

db.menu_statistics.insertOne({
    menu_id: 1,
    menu_titre: "Menu Exemple",
    periode: "2024-12",
    nb_commandes: 0,
    chiffre_affaires: 0,
    created_at: new Date(),
    updated_at: new Date()
});

print('✅ Base de données MongoDB initialisée avec succès !');
print('📊 Collections créées : menu_statistics, ca_global');
print('🔍 Index créés pour optimiser les performances');