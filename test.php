<?php
/**
 * IPP Payload Decoder and Encoder
 *
 * Author: Aaron Jackson <aaron@aaronsplace.co.uk>
 *
 * References:
 *     https://datatracker.ietf.org/doc/html/rfc8010
 */

require 'app/HMS/Entities/Printers/IPPTypes.php';
use HMS\Entities\Printers\IPPTypes;

class IPPPayload
{
    const PACK_SIGNED_CHAR = 'c';
    const PACK_UNSIGNED_CHAR = 'C';
    const PACK_UNSIGNED_16_BE = 'n';
    const PACK_UNSIGNED_32_BE = 'N';

    protected $buffer;

    public function __construct()
    {

    }

    public function setBuffer($buffer) {
        $this->buffer = $buffer;
    }

    public function getBuffer() {
        return $this->buffer;
    }

    private function strdecode(&$offset) {
        $length = unpack(self::PACK_UNSIGNED_16_BE, $this->buffer, $offset)[1];
        $offset += 2;

        $value = substr($this->buffer, $offset, $length);
        $offset += $length;

        return $value;
    }

    private function strencode($value) {
        $this->buffer .= pack(self::PACK_UNSIGNED_16_BE, strlen($value));
        $this->buffer .= $value;
    }

    private function langstrdecode(&$offset) {
        $lang = $this->strdecode($offset);
        $value = $this->strdecode($offset);

        return [
            'lang' => $lang,
            'value' => $value,
        ];
    }

    private function langstrencode($value) {
        $this->strencode($value['lang']);
        $this->strencode($value['value']);
    }

    private function intdecode(&$offset) {
        $int = unpack(self::PACK_UNSIGNED_32_BE, $this->buffer, $offset + 2)[1];
        $offset += 6;

        return $int;
    }

    private function intencode($value) {
        $this->buffer .= pack(self::PACK_UNSIGNED_16_BE, 0);
        $this->buffer .= pack(self::PACK_UNSIGNED_32_BE, $value);
    }

    private function booldecode(&$offset) {
        $int = unpack(self::PACK_UNSIGNED_CHAR, $this->buffer, $offset + 2)[1];
        $offset += 3;

        return $int === IPPTypes::TRUE;
    }

    private function boolencode($value) {
        $this->buffer .= pack(self::PACK_UNSIGNED_16_BE, 1); // value-length
        if ($value) {
            $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, IPPTypes::TRUE);
        } else {
            $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, IPPTypes::FALSE);
        }
    }

    private function enumdecode(&$offset) {
        $int = unpack(self::PACK_UNSIGNED_32_BE, $this->buffer, $offset + 2)[1];
        $offset += 6;

        return $int;
    }

    private function enumencode($value) {
        $this->buffer .= pack(self::PACK_UNSIGNED_16_BE, 4) .
                         pack(self::PACK_UNSIGNED_32_BE, $value);
    }

    private function datedecode(&$offset) {
        $drift = unpack(self::PACK_SIGNED_CHAR, $this->buffer, $offset + 11)[1] * 60 +
                 unpack(self::PACK_SIGNED_CHAR, $this->buffer, $offset + 12)[1];
        if (substr($this->buffer, $offset + 10, 1) === '+') {
            $drift *= -1;
        }

        // There is also milliseconds at `offset + 9`, but we'll ignore that
        $date = mktime(
            unpack(self::PACK_UNSIGNED_CHAR,  $this->buffer, $offset + 6)[1],          // hours
            unpack(self::PACK_UNSIGNED_CHAR,  $this->buffer, $offset + 7)[1] + $drift, // minutes
            unpack(self::PACK_UNSIGNED_CHAR,  $this->buffer, $offset + 8)[1],          // seconds
            unpack(self::PACK_UNSIGNED_CHAR,  $this->buffer, $offset + 4)[1],          // month
            unpack(self::PACK_UNSIGNED_CHAR,  $this->buffer, $offset + 5)[1],          // day
            unpack(self::PACK_UNSIGNED_16_BE, $this->buffer, $offset + 2)[1]           // year
        );

        $offset += 13;

        return $date;
    }

    private function dateencode($value) {
        $drift = (int)date('Z', $value);
        $driftHours = (int)(abs($drift) / 60);
        $driftMinutes = (int)(abs($drift) % 60);

        $this->buffer .= pack(self::PACK_UNSIGNED_16_BE, 11) .
                         pack(self::PACK_UNSIGNED_16_BE, (int)date('Y', $value)) . // year
                         pack(self::PACK_SIGNED_CHAR, (int)date('n', $value)) .    // month
                         pack(self::PACK_SIGNED_CHAR, (int)date('j', $value)) .    // day
                         pack(self::PACK_SIGNED_CHAR, (int)date('G', $value)) .    // hours
                         pack(self::PACK_SIGNED_CHAR, (int)date('i', $value)) .    // minutes
                         pack(self::PACK_SIGNED_CHAR, (int)date('s', $value)) .    // seconds
                         pack(self::PACK_SIGNED_CHAR, 0) .                         // milliseconds
                         ($drift > 0 ? '-' : '+') .                                // tz drift -/+
                         pack(self::PACK_SIGNED_CHAR, $driftHours) .               // tz hours
                         pack(self::PACK_SIGNED_CHAR, $driftMinutes);              // tz minutes
    }

    private function collectiondecode(&$offset) {
        $collection = [];
        $offset += 2;

        $index = -1;
        while (unpack(SELF::PACK_UNSIGNED_CHAR, $this->buffer, $offset)[1] !== IPPTypes::END_COLLECTION) {
            $tag = unpack(self::PACK_UNSIGNED_CHAR,  $this->buffer, $offset++)[1];

            if ($tag == IPPTypes::MEMBER) {
                $index++;
                $collection[$index] = [
                    'tag' => $tag,
                    '_tagName' => IPPTypes::TYPE_STRINGS[$tag] . " [0x" . dechex($tag) . "]",
                    '_debug' => 'collection',
                    'name' => '',
                    'value' => $this->memberdecode($offset),
                ];
            } else {
                $nextTag = $tag;
                while ($nextTag == $tag) {
                    if ($nextTag == IPPTypes::KEYWORD) {
                        $name = $this->strdecode($offset); // always null
                    }
                    $collection[$index]['value']['value'][] = $this->decodeTag($offset, $tag);
                    $nextTag = unpack(self::PACK_UNSIGNED_CHAR, $this->buffer, $offset + 1)[1];
                }
            }
        }

        // skip over the END_COLLECTION tag, as well as null end-name-length and end-name-value
        $offset += 5;

        return $collection;
    }

    private function collectionencode($members) {
        $this->buffer .= pack(self::PACK_UNSIGNED_16_BE, 0x0000); // name-value

        if (array_key_exists('tag', $members['value'])) {
            $members['value'] = [ $members['value'] ];
        }

        foreach ($members['value'] as $member) {
            $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, $member['tag']);
            if ($member['tag'] == IPPTypes::MEMBER) {
                //$this->encodeTag($member['value'], $member['tag']);
                $this->memberencode($member['value'], $member['tag']);
            }
        }

        $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, IPPTypes::END_COLLECTION) .
                         pack(self::PACK_UNSIGNED_16_BE, 0) . // end-name-length
                         pack(self::PACK_UNSIGNED_16_BE, 0);  // end-name-value

    }

    private function memberdecode(&$offset) {
        $offset += 2;  // skip name-length (always 0x0000)

        $memberName = $this->strdecode($offset);

        $memberValueTag = unpack(self::PACK_UNSIGNED_CHAR,  $this->buffer, $offset++)[1];
        $offset += 2;  // skip name-length (always 0x0000)

        $value = $this->decodeTag($offset, $memberValueTag);

        return [
            'tag' => $memberValueTag,
            '_tagName' => IPPTypes::TYPE_STRINGS[$memberValueTag] . " [0x" . dechex($memberValueTag) . "]",
            'name' => $memberName,
            'value' => [ $value ],
        ];
    }

    private function memberencode($value) {
        $this->buffer .= pack(self::PACK_UNSIGNED_16_BE, 0x0000); // name-length
        $this->strencode($value['name']);
        $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, $value['tag']) .
                         pack(self::PACK_UNSIGNED_16_BE, 0x0000); // name-length
        //$this->encodeTag($value['value'], $value['tag']);

        foreach ($value['value'] as $memberValue) {
            $this->encodeTag($memberValue, $value['tag']);
        }
    }

    private function decodeTag(&$offset, $tag) {
        $value = null;

        switch($tag) {
            case IPPTypes::INTEGER:
                $value = $this->intdecode($offset);
                break;

            case IPPTypes::BOOLEAN:
                $value = $this->booldecode($offset);
                break;

            case IPPTypes::ENUM:
                $value = $this->enumdecode($offset);
                break;

            case IPPTypes::DATE_TIME:
                $value = $this->datedecode($offset);
                break;

            case IPPTypes::BEGIN_COLLECTION:
                $value = $this->collectiondecode($offset);
                break;

            case IPPTypes::MEMBER:
                $value = $this->memberdecode($offset);
                break;

            case IPPTypes::TEXT_WITH_LANG:
            case IPPTypes::NAME_WITH_LANG:
                $value = $this->langstrdecode($offset);
                break;

            default:
                $value = $this->strdecode($offset);
        }

        return $value;
    }

    private function encodeTag($value, $tag) {
        if (is_array($value) && ! array_key_exists('value', $value)) {
            $i = 1;
            foreach ($value as $subValue) {
                $this->encodeTag($subValue, $tag);
                if ($i++ < count($value)) {
                    $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, $tag);
                    $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, 0) . pack(self::PACK_UNSIGNED_CHAR, 0);
                }
            }
            return;
        }

        switch ($tag) {
            case IPPTypes::INTEGER:
                $this->intencode($value);
                break;

            case IPPTypes::BOOLEAN:
                $this->boolencode($value);
                break;

            case IPPTypes::ENUM:
                $this->enumencode($value);
                break;

            case IPPTypes::DATE_TIME:
                $this->dateencode($value);
                break;

            case IPPTypes::BEGIN_COLLECTION:
                $this->collectionencode($value);
                break;

            case IPPTypes::MEMBER:
                $this->memberencode($value);
                break;

            case IPPTypes::TEXT_WITH_LANG:
            case IPPTypes::NAME_WITH_LANG:
                $this->langstrencode($value);
                break;

            default:
                $this->strencode($value);
        }
    }

    function decode($start = 0, $end = 0) {
        $buffer = &$this->buffer;
        if (! $end) $end = strlen($buffer);

        $offset = $start;

        $object = [
            'version' => [],
            'groups' => [],
            'data' => null,
        ];

        $object['version']['major'] = unpack(self::PACK_SIGNED_CHAR, $buffer, $offset++)[1];
        $object['version']['minor'] = unpack(self::PACK_SIGNED_CHAR, $buffer, $offset++)[1];

        // This should be a signed int but PHP's unpack function doesn't support that explicitly.
        $object['operationIdOrStatusCode'] = unpack(self::PACK_UNSIGNED_16_BE, $buffer, $offset)[1];
        $offset += 2;

        $object['requestId'] = unpack(self::PACK_UNSIGNED_32_BE, $buffer, $offset)[1];
        $offset += 4;

        $tag = unpack(self::PACK_UNSIGNED_CHAR, $buffer, $offset++)[1];
        while ($tag !== IPPTypes::END_OF_ATTRIBUTES_TAG && $offset < $end) {
            $group = [
                'tag' => $tag,
                '_tagName' => IPPTypes::TYPE_STRINGS[$tag] . " [0x" . dechex($tag) . "]",
                'attributes' => [],
            ];

            $tag = unpack(self::PACK_UNSIGNED_CHAR, $buffer, $offset++)[1];
            $values = [];
            while ($tag >= IPPTypes::UNSUPPORTED) {
                $nameTest = $this->strdecode($offset);
                if ($nameTest) {
                    $name = $nameTest;
                }

                $value = $this->decodeTag($offset, $tag);

                if (array_key_exists($name, $group['attributes'])) {
                    $group['attributes'][$name]['value'][] = $value;
                } else {
                    // This check is for debugging only and should be removed later.
                    if (! array_key_exists($tag, IPPTypes::TYPE_STRINGS)) {
                        echo "$tag (0x" . dechex($tag) . ") does not exist in TYPE_STRINGS, attr name $name\n";
                    }
                    $group['attributes'][$name] = [
                        'tag' => $tag,
                        '_tagName' => IPPTypes::TYPE_STRINGS[$tag] . " [0x" . dechex($tag) . "]",
                        'name' => $name,
                        'value' => [ $value ],
                    ];
                }

                $tag = unpack(self::PACK_UNSIGNED_CHAR, $buffer, $offset++)[1];
            }

            $object['groups'][] = $group;
        }

        if ($end > $offset) {
            $object['data'] = substr($this->buffer, $offset);
        }

        return $object;
    }

    function encode($payload) {
        $this->buffer = pack(self::PACK_UNSIGNED_CHAR, $payload['version']['major']) .
                        pack(self::PACK_UNSIGNED_CHAR, $payload['version']['minor']) .
                        pack(self::PACK_UNSIGNED_16_BE, $payload['operationIdOrStatusCode']) .
                        pack(self::PACK_UNSIGNED_32_BE, $payload['requestId']);

        foreach ($payload['groups'] as $group) {
            $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, $group['tag']);

            foreach ($group['attributes'] as $attribute) {
                $index = 0;

                //$values = is_array($attribute['value']) ? $attribute['value'] : [ $attribute['value'] ];
                $values = $attribute['value'];

                if (! is_array($values) || ! array_key_exists('value', $values)) {
                    $values = [ $values ];
                }

                foreach ($values as $value) {
                    $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, $attribute['tag']);
                    if (($index++) === 0) {
                        $this->strencode($attribute['name']);
                    } else {
                        $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, 0) . pack(self::PACK_UNSIGNED_CHAR, 0);
                    }

                    $this->encodeTag($value, $attribute['tag']);
                }
            }
        }

        $this->buffer .= pack(self::PACK_UNSIGNED_CHAR, IPPTypes::END_OF_ATTRIBUTES_TAG);
        $this->buffer .= $payload['data'];
    }
}

$buffer = file_get_contents('response');

$test = new IPPPayload();
$test->setBuffer($buffer);

file_put_contents('response.decoded', print_r($test->decode(), true));

$reEncode = new IPPPayload();
$reEncode->encode($test->decode());

//file_put_contents('response.2nd', $reEncode->getBuffer());
//file_put_contents('response.2nd.decoded', print_r($reEncode->decode(), true));
