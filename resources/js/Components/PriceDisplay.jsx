export default function PriceDisplay({value, currency, color}) {
    return (
        <span className={`font-semibold ${color || 'text-gray-800'} dark:text-gray-200`}>
    {value} {currency}
  </span>
    );
}
