Zammad
===

Installation via Composer :
===

`composer require dogteam/zammad`


Configuration :
===

Ajouter les champs suivants dans votre fichier `.env`


`ZAMMAD_URL`: L'URL de votre installation Zammad

Pour vous authentifier vous avez le choix entre :

`ZAMMAD_USERNAME`: Votre nom d'utilisateur Zammad
`ZAMMAD_PASSWORD`: Votre mot de passe Zammad

ou

`ZAMMAD_TOKEN`: Un jeton d'authentification pour vous connecter


Utilisation :
===

Exemple :

```php
use Dogteam\Zammad\Zammad;

class Controller extends Controller {

    public function test(){
    
        $t = new Zammad;
        
        //Un array contenant les informations d'un ticket
        $ticket_data = [
            'title'            => 'exemple',
            'customer'         => 'exemple@exemple.exemple',
            'group'            => 'exemple',
            'article'          => [
                'from'         => 'exemple',
                'subject'      => 'exemple',
                'body'         => 'exemple',
                'cc'           => 'exemple1@exemple.exemple',
                'to'           => 'exemple47@exemple.exemple',
                'from'         => 'exemple2@exemple.exemple',
                'type'         => 'email',
            ],
        ];
        
        //Créer un ticket
        $t->create('ticket',$ticket_data);
        
        //Mettre à jour un ticket
        $t->update($id_ticket, $ticket_data);
        
        //Supprimer un ticket
        $t->delete('ticket',$id_ticket);
        
        //Chercher un ticket avec des mots-clés
        $t->search('ticket',$mot_cle);
        
        //Chercher un ticket avec l'id
        $t->find('ticket', $id_ticket);
        
        //Afficher tous les tickets
        $t->all('ticket');
    }
}
```

Les méthodes fonctionnent toutes de la même manière :
```php
$objet->fonction($type)
```
Le type est ce que vous voulez modifier parmis :

*  ticket           (ticket)
*  user             (utilisateur)
*  group            (groupe)
*  organization     (organisation)
*  ticket_priority  (les différentes priorités de ticket (haute, normale, basse, etc...))
*  ticket_state     (les différents états de ticket (ouvert, fermé, en attente, etc...))

La plupart des méthodes requiert d'autres paramètres :
```php
create($type, $array);
update($type, $id, $array);
delete($type, $id);
search($type, $string);
find($type, $id);
all($type);
```

Le contenu de `$array` varie en fonction du type utilisé :

* Ticket
```php

        $ticket_data = [
            'title'            => 'exemple',
            'customer'         => 'exemple@exemple.exemple',
            'group'            => 'exemple',
            'article'          => [
                'from'         => 'exemple',
                'subject'      => 'exemple',
                'body'         => 'exemple',
                'cc'           => 'exemple1@exemple.exemple',
                'to'           => 'exemple47@exemple.exemple',
                'from'         => 'exemple2@exemple.exemple',
                'type'         => 'email',
            ],
        ];
```

* User

```php
        $user_data = [
            "id" => 1
            "organization_id" => null
            "login" => "user@user.com"
            "firstname" => ""
            "lastname" => ""
            "email" => "user@user.com"
      ],
```
