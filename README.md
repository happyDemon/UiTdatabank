# UitDatabank API wrapper

Met deze wrapper kan je makkelijk verbinding maken met UITdatabank's API.

De UiTdatabank is de rijkste bron van evenementen en activiteiten in Vlaanderen en Brussel vb. films, concerten, theater, ... zie [www.uitinvlaanderen.be](http://www.uitinvlaanderen.be). 
UiTID is een user platform met cultuurprofielen van meer dan 70.000 cultuur- en vrijetijdszoekers.

Er word gebruikt gemaakt van [cultuurnet/cdb](https://github.com/cultuurnet/Cdb) om de binnenkomende data te converteren.

[UITdatabank](http://tools.uitdatabank.be/)

### Voorbeeld

```
use HappyDemon\UitDatabank\UitDatabank;

$uitDatabank = new UitDatabank(
        env('UIT_KEY'),// Jouw UIT API key
        env('UIT_SECRET'), // Jouw UIT API secret
        env('UIT_SERVER') // UIT server (basis foor endpoints)
    );
    
    try {
        $filter = new Filter();
        $filter->setQ('*:*');
        $filter->setGroup('event');
        $filter->setRows(300);
        $response = $uitDatabank->search($filter);
        
    } catch (\Exception $e) {
        die(var_dump($e));
    }
    
    // output
    var_dump($response->getEvents());
```