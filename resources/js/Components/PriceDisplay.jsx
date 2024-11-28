export default function PriceDisplay({value, currency}) {
    return (
        <span className="font-semibold text-gray-800 dark:text-gray-200">
    {value.toFixed(2)} {currency}
  </span>
    );
}
