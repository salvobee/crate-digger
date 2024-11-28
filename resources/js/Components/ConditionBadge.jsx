export default function  ConditionBadge ({ condition }) {
    const colors = {
        "Mint (M)": 'bg-green-600 text-white',
        "Near Mint (NM or M-)": 'bg-green-400 text-white',
        "Very Good Plus (VG+)": 'bg-green-200 text-green-600',
        "Very Good (VG)": 'bg-green-100 text-green-400',
        "Good Plus (G+)": 'bg-yellow-200 text-yellow-600',
        "Good (G)": 'bg-yellow-100 text-yellow-400',
        "Poor": 'bg-gray-500 text-white',
        "Fair": 'bg-gray-500 text-white',
        "Not Graded": 'bg-gray-300 text-black',
    };


    function extractParentheses(text) {
        const match = text.match(/\((.*?)\)/); // Cattura il contenuto tra parentesi
        return match ? match[1] : text; // Ritorna il contenuto trovato o il testo originale
    }

    return (
        <span
            className={`text-nowrap px-2 py-1 text-xs font-semibold rounded ${colors[condition] || 'bg-gray-200'}`}
        >
      {extractParentheses(condition)}
    </span>
    );
};
