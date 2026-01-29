<?php

namespace RRZE\BlockControl\Blocks;

defined('ABSPATH') || exit;


/**
 * Class BlockControl
 *
 * ermittelt die aktuelle Nutzerrolle, fragt erlaubte Blöcke ab (Settings, getOption())
 * entfernt nicht erlaubte Blöcke aus dem Editor
 */
class BlockControl
{
    // TODO: Konstruktor noch hinzufügen fürs initiieren der KLassen
//Abschließend sollte BlockControl nur
//noch Settings::generateWhitelist() aufrufen, um die erlaubten Blöcke zu kennen.

    /**
     * ermittelt die aktuelle User Rolle
     * @returns string
     *
     */


    public function getCurrentUserRole(): string
    {
        /** ermittelt die aktuelle Nutzerrolle */


    }

    /**
     * ermittelt, welche Blöcke der aktuelle User verwenden darf
     * @returns array
     *
     */
    public function getAllowedBlocksByUserRole($userRole): string
    {
        // gibt die Blöcke für die gewählte Nutzerrolle zurück als Array
    }


    /**
     * Rein exemplarisch. Ob du die Funktion dann wirklich brauchst, merkst du spätestens beim Umsetzen.
     * @param $userRole
     * @param $whitelist
     * @return void
     */
    public function prepareWhiteListForCurrentUser($userRole, $whitelist)
    {
        /** Vergleich: registrierte Blöcke und erlaubte Blöcke*/

        // Registrierte Blöcke abrufen über separate Funktion

        // Die Funktion nimmt die Nutzerrolle und die WhiteList für diese Nutzerrolle
        // und verarbeitet sie vielleicht irgendwie. Stell dir vor der Hook nimmt nur eine Liste an erlauben Blöcken,
        // dann könnte man die Funktion hier bestimmt skippen.

        // Falls der Hook vielleicht eine ARt Blacklist ist. Kann es ja sein, dass du zuerst alle Blöcke abrufen musst
        // und dann die Blöcke der WhiteList rausnehmen musst.

        // Stößt applyBlockControls() an.
    }


    /**
     * wendet die Blockeinschränkungen im Editor an
     * @returns array
     *
     */
    public function applyBlockControls(): string
    {
        /** Entfernen aller Blöcke aus dem Editor, die nicht in der erlaubten Liste enthalten sind*/


    }
}
