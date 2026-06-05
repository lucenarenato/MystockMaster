<?php

declare(strict_types=1);

namespace App\Support;

use Gloudemans\Shoppingcart\Cart as BaseCart;

class Cart extends BaseCart
{
    /**
     * Returns total discount amount summing product_discount * qty across all items.
     */
    public function discount(): float
    {
        return $this->content()->reduce(function (float $carry, $item): float {
            $discount = (float) ($item->options->product_discount ?? 0);

            return $carry + ($discount * $item->qty);
        }, 0.0);
    }

    /**
     * Sets the same tax rate on every item currently in the cart.
     */
    public function setGlobalTax(int $taxRate): void
    {
        foreach ($this->content() as $item) {
            $this->setTax($item->rowId, $taxRate);
        }
    }

    /**
     * Applies a percentage discount to every item currently in the cart.
     */
    public function setGlobalDiscount(int $discount): void
    {
        foreach ($this->content() as $item) {
            $discountAmount = $discount > 0
                ? round($item->price * ($discount / 100), 2)
                : 0.0;

            $this->update($item->rowId, [
                'options' => array_merge($item->options->toArray(), [
                    'product_discount'      => $discountAmount,
                    'product_discount_type' => 'fixed',
                ]),
            ]);
        }
    }
}
