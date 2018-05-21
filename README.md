# Test Symfony

## RENDU

Le but de l'exercice est donc de traiter des fichiers HL7 en insérant les données récupérer en BDD.
L'action de traitement des fichiers se touve dans le DefaultController 'traitementFichierAction', j'utilise donc 
le finder de symfony pour récupérer tous les fichiers et ainsi traviller sur chaque fichier, l'action fait appel à 
un service 'TraitemntFichier.php' oû tout le code métier s y trouve.
Etant donnée qu'on sait ce qu'on veut récupérer et que les fichiers sont corrects, j'ai tout simplement utilisé des 
fonctions php (susbstr, explode).

*Voici quelques captures d'écran*
- Vue avant tratiement

![alt text](https://github.com/ah-sed/test-symfony/blob/master/web/captures/capture1.PNG)

- Vue aprés traitement

![alt text](https://github.com/ah-sed/test-symfony/blob/master/web/captures/capture2.PNG)

- Vue liste patients

![alt text](https://github.com/ah-sed/test-symfony/blob/master/web/captures/capture3.PNG)

- Vue liste médecins

![alt text](https://github.com/ah-sed/test-symfony/blob/master/web/capture/capture4.PNG)