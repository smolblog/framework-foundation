<?php

namespace Smolblog\Foundation\Value\Traits;

use Smolblog\Foundation\Exceptions\InvalidValueProperties;
use Throwable;

trait SerializableSupertypeKit {
	use SerializableValueKit {
		serializeValue as protected baseSerialize;
		deserializeValue as protected baseDeserialize;
	}

	/**
	 * Serialize the object.
	 *
	 * Adds the `type` property to the serialized object.
	 *
	 * @return array
	 */
	public function serializeValue(): array {
		$base = $this->baseSerialize();
		$base['type'] = get_class($this);
		return $base;
	}

	/**
	 * Override this function to set a fallback class if the given type does not exist or is not a subtype of
	 * this class.
	 *
	 * @return string|null
	 */
	private static function getFallbackClass(): ?string {
		return null;
	}

	/**
	 * Deserialize the object using the type property.
	 *
	 * @throws InvalidValueProperties When the class cannot be deserialized.
	 *
	 * @param array $data Serialized object.
	 * @return static
	 */
	public static function deserializeValue(array $data): static {
		$type = $data['type'] ?? null;
		if (isset($type) && class_exists($type) && is_a($type, static::class, allow_string: true)) {
			unset($data['type']);
			return $type::deserializeValue($data);
		}

		if (self::class == static::class) {
			$fallback = static::getFallbackClass();
			if (isset($fallback) && class_exists($fallback)) {
				return $fallback::deserializeValue($data);
			}
		}

		try {
			return static::baseDeserialize($data);
		} catch (Throwable $e) {
			throw new InvalidValueProperties(
				"Unable to deserialize $type from " . static::class . ': ' . $e->getMessage(),
				previous: $e
			);
		}
	}
}
