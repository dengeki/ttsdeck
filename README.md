# ttsdeck

PHP helper class for generating custom decks for card games in
Tabletop Simulator. Has been tested with the Legend of the Five Rings (L5R)
Living Card Game. May or may not work with other card games.

Example usage:

```php
require_once "ttsdeck/ttsdeck.php";

// Game JSON resides in the mods directory of TTS
$deck = new TtsDeck(json_decode(file_get_contents("./game.json")));

// Create face-up pile of cards (optional)
$deck->add_pile("SomeCards", true);

// Add cards by name (partial match, case insensitive)
$deck->add_card("Champion of the Gods");
$deck->add_card("Purgatory");

// Create another pile, face-down
$deck->add_pile("OtherCards", false);

// Add a card to the new pile
$deck->add_card("Walking Dead");

// Encode the deck
$serialized = json_encode($deck->get_deck(),
  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
```
