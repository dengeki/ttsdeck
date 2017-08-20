# ttsdeck

PHP helper class for generating custom decks for card games in
Tabletop Simulator. Has been tested with the Legend of the Five Rings (L5R)
Living Card Game. May or may not work with other card games.

Example usage:

```php
require_once "ttsdeck/ttsdeck.php";

// Game JSON resides in the mods directory of TTS
// (/path/to/Tabletop Simulator/Mods/Workshop)
$deck = new TtsDeck(json_decode(file_get_contents("./game.json")));

// Create face-up pile of cards (optional)
$deck->add_pile(true, "SomeCards");

// Add cards by name (partial match, case insensitive)
// Returns an array of matched card names
// Success if count($result) === 1
$deck->add_card("Champion of the Gods");
$deck->add_card("Purgatory");

// Create another pile, face-down
$deck->add_pile(false, "OtherCards");

// Add a card to the new pile (exact match, case insensitive)
$deck->add_card("Walking Dead", false);

// Encode the deck
$serialized = json_encode($deck->get_deck(),
  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
```
