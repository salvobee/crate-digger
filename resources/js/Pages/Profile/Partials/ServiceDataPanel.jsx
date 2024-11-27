import {useState} from "react";
import {useForm} from "@inertiajs/react";
import DangerButton from "@/Components/DangerButton.jsx";
import Modal from "@/Components/Modal.jsx";
import SecondaryButton from "@/Components/SecondaryButton.jsx";


export default function ServiceDataPanel({ service, className = '' }) {
    const {
        delete: destroy,
        processing,
        reset,
        clearErrors,
    } = useForm({});

    const [confirmingAccountDeletion, setConfirmingAccountDeletion] = useState(false);

    const confirmAccountDeletion = () => setConfirmingAccountDeletion(true);

    const deleteAccount = (e) => {
        e.preventDefault();

        destroy(route('discogs.destroy'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
            onError: () => alert('errore'),
            onFinish: () => reset(),
        });
    };

    const closeModal = () => {
        setConfirmingUserDeletion(false);

        clearErrors();
        reset();
    };

    return (
        <>
        <section className={`space-y-6 ${className}`}>
            <header>
                <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Discogs Account Data
                </h2>
            </header>

            <div>
                <pre className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {JSON.stringify(service)}
                </pre>
                <DangerButton onClick={confirmAccountDeletion}>
                    Delete Account
                </DangerButton>
            </div>

        </section>

            <Modal show={confirmingAccountDeletion} onClose={closeModal}>
                <form onSubmit={deleteAccount} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Are you sure you want to delete your account?
                    </h2>

                    <p className="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        Once your account is deleted, you will not be able to browse your discogs content.
                    </p>


                    <div className="mt-6 flex justify-end">
                        <SecondaryButton onClick={closeModal}>
                            Cancel
                        </SecondaryButton>

                        <DangerButton className="ms-3" disabled={processing}>
                            Delete Account
                        </DangerButton>
                    </div>
                </form>
            </Modal>
        </>
    );
}
