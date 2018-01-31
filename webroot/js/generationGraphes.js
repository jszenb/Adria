/*********************************************************************/
//* Script generationGraphes.js                                      */
/* A n'utiliser que sur phantomjs                                    */
/* Ce script est appelé par le contrôleur EntiteDocs pour produire   */
/* les images nécessaires à la fiche entité documentaire. Il permet  */
/* de générer les images des graphiques de cette fiche.              */
/* Entrée : - paramètre 1 : la page HTML permettant de générer un    */
/*                          graphe                                   */
/*          - paramètre 2 : le fichier png de sortie qui sera inséré */
/*                          dans la fiche.                           */
/*********************************************************************/
var args = require('system').args;
var page = require('webpage').create();
var settings = {encoding: "utf8"};

// Récupération des deux paramètres
var address = args[1];
var outputFile = args[2];


page.open(address, settings, function (status) {
    // Si la page donnée en paramètre 1 ne s'est pas ouverte, on remonte une erreur
    if (status !== 'success') {
        console.log('Failed to load address '+address+' ' + page.reason_url + ": " + page.reason + " " + status);
        phantom.exit(-1);
    }
    else {
		page.render(outputFile);
		phantom.exit();
    }
}); 	