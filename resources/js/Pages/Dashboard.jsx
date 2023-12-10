import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";

export default function Dashboard({ auth }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <h2 className="font-semibold text-xl text-gray-800 leading-tight">
                    Évolutions à venir / envisagées
                </h2>
            }
        >
            <Head title="Évolutions" />

            <div className="flex justify-center py-12 ">
                <div className="space-x-20 sm:-my-px sm:ml-10 flex justify-center">
                    <div className="w-4/12">
                        <h1 className="font-semibold">Évolutions</h1>
                        <ul className="list-disc px-4 mt-3">
                            <li>
                                Ajouter un bouton "Demander l'accès" qui envoie
                                un mail au propriétaire de la liste
                            </li>
                            <li>Avoir un récap de ses achats</li>
                            <li>
                                Pouvoir ajouter des photos dans le formulaire
                                d'ajout d'une idée
                            </li>
                        </ul>
                    </div>
                    <div className="w-4/12">
                        <h1 className="font-semibold">Bugs</h1>
                        <ul className="list-disc px-4 mt-3">
                            <li>Focus de la page des listes</li>
                            <li>
                                Mettre à jour la liste lorsqu'on
                                modifie/supprime une idée (sans avoir à
                                recharger la page)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
