import { useState } from "react";
import SecondaryButton from "@/Components/SecondaryButton.jsx";
import Modal from "@/Components/Modal.jsx";

export default function Facet({ facet, onChange }) {
    const [isExpanded, setIsExpanded] = useState(false);
    const [showModal, setShowModal] = useState(false);

    // Ordiniamo inizialmente per il numero totale, come da requisito
    const sortedOptions = [...facet.options].sort((a, b) => b.total - a.total);

    // Se il facet supporta l'ordinamento alfabetico espanso, cambiamo l'ordinamento
    const expandedOptions = facet.sort_alphabetically && isExpanded
        ? [...facet.options].sort((a, b) => {
            const valueA = a.value?.toString() || ""; // Converti in stringa se necessario
            const valueB = b.value?.toString() || "";
            return valueA.localeCompare(valueB);
        })
        : sortedOptions;

    const visibleOptions = expandedOptions.slice(0, 5);
    const hiddenOptions = expandedOptions.slice(5);


    // Funzioni per apertura/chiusura del Modal
    const openModal = () => setShowModal(true);
    const closeModal = () => setShowModal(false);

    // Ordinamento alfabetico per il Modal (se specificato)
    const modalOptions = facet.sort_alphabetically
        ? [...hiddenOptions].sort((a, b) => {
            const valueA = a.value?.toString() || '';
            const valueB = b.value?.toString() || '';
            return valueA.localeCompare(valueB);
        })
        : hiddenOptions;

    // Trova il valore massimo di "total" per calcolare la larghezza massima della barra
    const maxTotal = Math.max(...sortedOptions.map(option => option.total));

    return (
        <div className="flex flex-col space-y-2">
            <h3 className="font-bold">{facet.title}</h3>
            <ul className="space-y-1">
                {visibleOptions.map(option => {
                    // Calcola la larghezza della barra in percentuale
                    const barWidth = (option.total / maxTotal) * 100;
                    return (
                        <li key={option.value}>
                            <button
                                onClick={() => onChange(facet.fieldname, option.value)}
                                className={`text-left ${option.selected ? "font-bold underline" : ""}`}
                            >
                                {option.value} ({option.total})
                            </button>
                            {/* Barra sotto il link */}
                            <div className="w-full h-2 mt-1 bg-gray-200 rounded">
                                <div
                                    className="h-full bg-blue-500 rounded"
                                    style={{width: `${barWidth}%`}}
                                />
                            </div>
                        </li>
                    )
                })}
                {hiddenOptions.length > 0 && (
                    <>
                        {isExpanded && (
                            hiddenOptions.map(option => {
                                const barWidth = (option.total / maxTotal) * 100;
                                return (
                                    <li key={option.value}>
                                        <button
                                            onClick={() => onChange(facet.fieldname, option.value)}
                                            className={`text-left ${option.selected ? "font-bold underline" : ""}`}
                                        >
                                            {option.value} ({option.total})
                                        </button>
                                        {/* Barra sotto il link */}
                                        <div className="w-full h-2 mt-1 bg-gray-200 rounded">
                                            <div
                                                className="h-full bg-blue-500 rounded"
                                                style={{width: `${barWidth}%`}}
                                            />
                                        </div>
                                    </li>
                                )
                            })
                        )}
                        <button
                            onClick={hiddenOptions.length > 30 ? openModal : () => setIsExpanded(!isExpanded)}
                            className="text-sm text-blue-500"
                        >
                            {hiddenOptions.length > 30
                                ? 'Show all in modal'
                                : isExpanded
                                    ? 'Show less'
                                    : 'Show more'}
                        </button>
                    </>
                )}
            </ul>

            {/* Modal per mostrare più di 30 opzioni */}
            {hiddenOptions.length > 30 && (
                <Modal show={showModal} onClose={closeModal}>
                    <div className="p-4">
                        <h3 className="font-bold text-lg mb-4">{facet.title}</h3>
                        {/* Griglia con 3 colonne */}
                        <ul className="grid grid-cols-3 gap-4 max-h-96 overflow-y-auto">
                            {modalOptions.map((option) => {
                                const barWidth = (option.total / maxTotal) * 100;
                                return (
                                    <li key={option.value} className="break-words">
                                        <button
                                            onClick={() => {
                                                onChange(facet.fieldname, option.value);
                                                closeModal();
                                            }}
                                            className={`text-left ${option.selected ? 'font-bold underline' : ''}`}
                                        >
                                            {option.value} ({option.total})
                                        </button>
                                        {/* Barra sotto il link */}
                                        <div className="w-full h-2 mt-1 bg-gray-200 rounded">
                                            <div
                                                className="h-full bg-blue-500 rounded"
                                                style={{ width: `${barWidth}%` }}
                                            />
                                        </div>
                                    </li>

                                )
                            })}
                        </ul>
                        <div className="mt-4 flex justify-end">
                            <SecondaryButton onClick={closeModal}>Close</SecondaryButton>
                        </div>
                    </div>
                </Modal>
            )}
        </div>
    );
}
