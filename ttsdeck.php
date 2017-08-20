<?php

require_once "util.php";

class TtsDeck
{
  private $game_data;
  private $cards = [];
  private $deck;
  private $current_pile_index;
  private $current_custom_deck_index;
  private $card_sets;

  public function __construct($game_data) {
    $this->game_data = $game_data;
    $this->import_cards();
    $this->clear();
  }

  public function add_pile($faceup, $name = "") {
    $this->current_custom_deck_index = 1;
    $this->current_pile_index++;
    $this->deck["ObjectStates"][$this->current_pile_index] = new ArrayObject([
      "Name" => "DeckCustom",
      "Nickname" => $name,
      "Transform" => new ArrayObject([
        "posX" => 4 + $this->current_pile_index * 2.5,
        "posY" => 10 + $this->current_pile_index * 2.5,
        "posZ" => -1,
        "rotX" => 0,
        "rotY" => 180,
        "rotZ" => $faceup ? 0 : 180,
        "scaleX" => 1,
        "scaleY" => 1,
        "scaleZ" => 1
      ]),
      "Description" => "",
      "ColorDiffuse" => new ArrayObject([
        "r" => 0.713235259,
        "g" => 0.713235259,
        "b" => 0.713235259
      ]),
      "Locked" => false,
      "Grid" => true,
      "Snap" => true,
      "SidewaysCard" => false,
      "LuaScript" => "",
      "LuaScriptState" => "",
      "DeckIDs" => [],
      "CustomDeck" => new ArrayObject(),
      "ContainedObjects" => [],
      "GUID" => sprintf("%06x", mt_rand(0, 0xffffff))
    ]);
  }

  public function add_card($partial_name) {
    if ($this->current_pile_index < 0) $this->add_pile();
    $matches = array_filter($this->cards, function($card) use ($partial_name) {
      if (!property_exists($card, "Nickname")) return false;
      $haystack = trim(strtolower($card->Nickname));
      $needle = trim(strtolower($partial_name));
      return strpos($haystack, $needle) !== false;
    });
    if (count($matches) !== 1) {
      return array_map(function($card) {
        return $card->Nickname;
      }, $matches);
    }
    $card = reset($matches);
    assert(count($card->CustomDeck) === 1);
    $custom_deck_entry = iterable_find($card->CustomDeck, function() {
      return true;
    })[1];
    $custom_deck_entry_match = iterable_find(
      $this->deck["ObjectStates"][$this->current_pile_index]["CustomDeck"],
      function($entry) use($custom_deck_entry) {
        return (
          $entry->FaceURL === $custom_deck_entry->FaceURL &&
          $entry->BackURL === $custom_deck_entry->BackURL
        );
      }
    );
    if ($custom_deck_entry_match === null) {
      $this->deck["ObjectStates"][$this->current_pile_index]["CustomDeck"]
        [$this->current_custom_deck_index] = $custom_deck_entry;
      $custom_deck_id = $this->current_custom_deck_index;
      $this->current_custom_deck_index++;
    } else {
      $custom_deck_id = $custom_deck_entry_match[0];
    }
    $remapped_card_id = (int)
      sprintf("%d%02d", $custom_deck_id, substr((string) $card->CardID, -2));
    $card_entry = new ArrayObject([
      "Name" => "Card",
      "Nickname" => $card->Nickname,
      "CardID" => $remapped_card_id,
      "Transform" => new ArrayObject([
        "posX" => 2.5,
        "posY" => 2.5,
        "posZ" => 3.5,
        "rotX" => 0,
        "rotY" => 180,
        "rotZ" => $this->deck["ObjectStates"][$this->current_pile_index]
          ["Transform"]["rotZ"],
        "scaleX" => 1,
        "scaleY" => 1,
        "scaleZ" => 1
      ])
    ]);
    array_push(
      $this->deck["ObjectStates"][$this->current_pile_index]["DeckIDs"],
      $remapped_card_id
    );
    array_push(
      $this->deck["ObjectStates"][$this->current_pile_index]
        ["ContainedObjects"], $card_entry
    );
    return [$card->Nickname];
  }

  public function get_deck() {
    return $this->deck;
  }

  public function get_card_sets() {
    return $this->card_sets;
  }

  private function clear() {
    $this->current_pile_index = -1;
    $this->deck = new ArrayObject([
      "SaveName" => "",
      "GameMode" => "",
      "Date" => "",
      "Table" => "",
      "Sky" => "",
      "Note" => "",
      "Rules" => "",
      "PlayerTurn" => "",
      "LuaScript" => "",
      "LuaScriptState" => "",
      "ObjectStates" => []
    ]);
  }

  private function import_cards($objects = null, $parent = null) {
    if ($objects === null) {
      $this->custom_deck_entries = new ArrayObject();
      $objects = $this->game_data->ObjectStates;
      $this->card_sets = [];
    }
    foreach ($objects as $object) {
      if (property_exists($object, "Name") &&
          strtolower($object->Name) === "deck" &&
          property_exists($object, "CustomDeck")) {
        foreach ($object->CustomDeck as $id => $obj) {
          if (!isset($this->custom_deck_entries[$id])) {
            $this->custom_deck_entries[$id] = $obj;
          }
        }
      }
      if (property_exists($object, "Name") &&
          strtolower($object->Name) === "card") {
        array_push($this->cards, $object);
        if ($parent && property_exists($parent, "Name") &&
            strtolower($parent->Name) === "deck" &&
            property_exists($parent, "Nickname")) {
          if (!isset($this->card_sets[$parent->Nickname])) {
            $this->card_sets[$parent->Nickname] = [];
          }
          $this->card_sets[$parent->Nickname][] = $object->Nickname;
        }
      } else if (property_exists($object, "ContainedObjects") &&
                 is_array($object->ContainedObjects)) {
        $this->import_cards($object->ContainedObjects, $object);
      }
    }
  }

}

?>
