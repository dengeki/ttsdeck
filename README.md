# ttsdeck

PHP helper class for generating custom decks for card games in
Tabletop Simulator. Has been tested with the Legend of the Five Rings (L5R)
Living Card Game. May or may not work with other card games.

Example usage:

```php
require_once "ttsdeck/ttsdeck.php";

$deck = new TtsDeck();

// Game JSON resides in the mods directory of TTS
// (/path/to/Tabletop Simulator/Mods/Workshop)
$game_json = json_decode(file_get_contents("./game.json"));
$deck->import_cards($game_json);

// Create face-up pile of cards (optional)
$deck->add_pile(true, "SomeCards");

// Add cards by name (partial match)
// Returns an array of matched card names
// Success if count($result) === 1
$deck->add_card("Champion of the Gods");
$deck->add_card("Purgatory");

// Create another pile, face-down
$deck->add_pile(false, "OtherCards");

// Add a card to the new pile (exact match)
$deck->add_card("Walking Dead", null, false);

// Add a card to the new pile with custom normalization function
$deck->add_card("IDontCAREabout spaces  and case", "normalize_card_name");

// Encode the deck as TTS-importable JSON
$serialized = json_encode($deck->get_deck(),
  JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

// Loop through all available cards
foreach ($deck->get_card_sets() as $set => $cards) {
  printf("Set %s:\n", $set);
  foreach ($cards as $card) {
    printf("- Card %s\n", $card);
  }
}

function normalize_card_name($name) {
  return str_replace(" ", "", strtolower($name));
}
```
