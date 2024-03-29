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

            <div className="max-w-4xl mx-auto pb-14 px-4 mt-6">
                <div className="space-x-0 sm:space-x-20 sm:flex">
                    <div className="">
                        <h1 className="font-semibold">Évolutions</h1>
                        <ul className="list-disc px-4 mt-3">
                            <li>
                                Organiser les idées par catégorie dans une liste
                            </li>
                            <li>
                                Permettre le tri/filtre des idées (par prix,
                                promo...)
                            </li>
                            <li>
                                Ajouter un bouton "Demander l'accès" qui envoie
                                un message au·à la propriétaire de la liste
                            </li>
                            <li>Permettre de ne plus suivre une liste</li>
                            <li>
                                Visualiser les personnes qui suivent nos listes
                            </li>
                            <li>Transférer une idée dans une autre liste</li>
                            <li>
                                Pop up pour inviter l'utilisateur·ice à ajouter
                                des idées lorsqu'il n'y en a plus beaucoup de
                                disponibles
                            </li>
                            <li>
                                Encart parrainage pour ne pas remettre l'info
                                dans chaque idée ?
                            </li>
                            <li>
                                Ajouter des photos dans le formulaire d'ajout
                                d'une idée
                            </li>
                            <li>Design du tableau "Mes achats"</li>
                        </ul>
                    </div>
                    <div className="sm:mt-0 mt-10">
                        <h1 className="font-semibold">Bugs à corriger</h1>
                        <ul className="list-disc px-4 mt-3">
                            <li>Focus de la page des listes</li>
                            <li>Modifier le mot de passe à la connexion</li>
                            <li>Drag&drop qui enregistre le changement</li>
                            <li>Pas de marque renseignée = null</li>
                        </ul>
                    </div>
                </div>

                {/* TOOLTIP */}
                <div className="group absolute right-0 justify-center hidden sm:block">
                    <button onClick={confirmListDeletion}>
                        <svg
                            xmlns="https://www.w3.org/2000/svg"
                            className="h-7 w-7 text-gray-300 hover:text-orange-500"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <span className="absolute top-10 left-0 scale-0 transition-all rounded bg-gray-900 p-2 text-xs text-center text-white group-hover:scale-100">
                        Une fois supprimée, pas de retour en arrière !
                    </span>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
