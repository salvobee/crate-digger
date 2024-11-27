import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import {Head, router, useForm} from '@inertiajs/react';
import { useState } from 'react';
import Modal from '@/Components/Modal';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';
import InputError from '@/Components/InputError';

export default function Index({ inventories }) {
    const [creatingStore, setCreatingStore] = useState(false);
    const [deletingStore, setDeletingStore] = useState(false);
    const [storeIdToDelete, setStoreIdToDelete] = useState(null);

    const {
        data,
        setData,
        post,
        processing,
        reset,
        errors,
        clearErrors,
    } = useForm();

    const openDeleteModal = (id) => {
        setStoreIdToDelete(id);
        setDeletingStore(true);
    };

    const closeDeleteModal = () => {
        setStoreIdToDelete(null);
        setDeletingStore(false);
    };

    const deleteStore = (e) => {
        e.preventDefault();

        if (!storeIdToDelete) return;

        router.delete(route('inventories.destroy', { inventory: storeIdToDelete }), {
            onSuccess: () => closeDeleteModal(),
        });
    };

    const openModal = () => setCreatingStore(true);

    const closeModal = () => {
        setCreatingStore(false);
        clearErrors();
        reset();
    };

    const createStore = (e) => {
        e.preventDefault();

        post(route('inventories.store'), {
            onSuccess: () => closeModal(),
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Record Stores
                </h2>
            }
        >
            <Head title="Record Stores" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
                    <div className="flex justify-end">
                        <PrimaryButton onClick={openModal}>Create Store</PrimaryButton>
                    </div>

                    <div className="bg-white p-4 shadow sm:rounded-lg sm:p-8 dark:bg-gray-800">
                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Avatar
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Name
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Rating
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Listings
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                            {inventories.map((inventory) => (
                                <tr key={inventory.id}>
                                    <td className="px-6 py-4">
                                        <img
                                            src={inventory.avatar_url}
                                            alt={`${inventory.seller_username} avatar`}
                                            className="h-10 w-10 rounded-full"
                                        />
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                        {inventory.seller_username}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                        {inventory.rating || 'N/A'}%
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-900 dark:text-gray-200">
                                        {inventory.total_listings_count || 'N/A'}
                                        {inventory.total_listings_count_updated_at && (
                                            <div className="text-xs text-gray-500">
                                                Checked at: {new Date(inventory.total_listings_count_updated_at).toLocaleDateString('it-IT')}
                                            </div>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 flex space-x-2 flex-row">
                                        <a
                                            href={inventory.html_url}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="flex space-x-1 items-center border border-black hover:bg-black hover:text-white text-black font-bold py-1 px-2 rounded"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 strokeWidth={1.5} stroke="currentColor" className="size-4">
                                                <path strokeLinecap="round" strokeLinejoin="round"
                                                      d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
                                            </svg>


                                            <span className="text-sm">View Store</span>
                                        </a>

                                        <button
                                            onClick={() => openDeleteModal(inventory.id)}
                                            className="
                                            flex space-x-1 items-center
                                            font-bold py-2 px-4 rounded
                                            border border-red-500 text-red-500
                                            hover:bg-red-500 hover:text-white"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 strokeWidth={1.5} stroke="currentColor" className="size-4">
                                                <path strokeLinecap="round" strokeLinejoin="round"
                                                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                            </svg>

                                            <span>Elimina</span>
                                        </button>
                                    </td>
                                </tr>
                            ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <Modal show={deletingStore} onClose={closeDeleteModal}>
                <div className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Conferma Eliminazione
                    </h2>
                    <p className="mt-4 text-sm text-gray-600 dark:text-gray-400">
                    Sei sicuro di voler eliminare questo negozio? L'operazione è irreversibile.
                    </p>
                    <div className="mt-6 flex justify-end">
                        <SecondaryButton onClick={closeDeleteModal}>Annulla</SecondaryButton>
                        <PrimaryButton
                            onClick={deleteStore}
                            className="ms-3 bg-red-500 hover:bg-red-600"
                        >
                            Elimina
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>

            <Modal show={creatingStore} onClose={closeModal}>
                <form onSubmit={createStore} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Create a New Store
                    </h2>

                    <div className="mt-4">
                        <InputLabel htmlFor="username" value="Username" />
                        <TextInput
                            id="username"
                            type="text"
                            value={data.username}
                            onChange={(e) => setData('username', e.target.value)}
                            className="mt-1 block w-full"
                            required
                            autoFocus
                        />
                        <InputError message={errors.username} className="mt-2" />
                    </div>

                    <div className="mt-6 flex justify-end">
                        <SecondaryButton onClick={closeModal}>Cancel</SecondaryButton>
                        <PrimaryButton className="ms-3" disabled={processing}>
                            Create
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>
        </AuthenticatedLayout>
    );
}
