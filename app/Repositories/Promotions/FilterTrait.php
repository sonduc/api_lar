<?php
namespace App\Repositories\Promotions;
use App\Repositories\GlobalTrait;

trait FilterTrait
{
   use GlobalTrait;
   /**
   * Scope Q
   * @author sonduc <ndson1998@gmail.com>
   *
   * @param $query
   * @param $q
   *
   * @return mixed
   */
   public function scopeQ($query, $q)
   {
      if ($q) {
         return $query->where('name', 'like', "%${q}%");
      }
      return $query;
   }

   /**
   * Scope Status
   * @author sonduc <ndson1998@gmail.com>
   *
   * @param $query
   * @param $q
   *
   * @return mixed
   */
   public function scopeStatus($query, $q)
   {
      if (is_numeric($q)) {
         $query->where('promotions.status', $q);
      }

      return $query;
   }

   /**
   * Scope Name
   * @author sonduc <ndson1998@gmail.com>
   *
   * @param $query
   * @param $q
   *
   * @return mixed
   */
   public function scopeName($query, $q)
   {
      if ($q) {
         $query->where('promotions.name', $q);
      }

      return $query;
   }

   /**
   * Scope Month
   * @author sonduc <ndson1998@gmail.com>
   *
   * @param $query
   * @param $q
   *
   * @return mixed
   */
   public function scopeMonth($query, $q)
   {
      if ($q) {
         $query->whereMonth('promotions.date_start', $q)
               ->whereMonth('promotions.date_end', $q)
               ->where('promotions.status', '1');
      }

      return $query;
   }
}
