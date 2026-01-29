# Schéma MongoDB - Statistiques

## Collection: commandes
- _id: ObjectId
- numeroCommande : string
- client : int
- menu : object
- menu.id : int
- menu.nom : string
- dateCommande : string (ISODate)
- dateCommandeDate : UTCDateTime
- statutCommande : string
- prixTotal : string