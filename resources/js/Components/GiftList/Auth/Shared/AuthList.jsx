import React, { useState } from "react";
import PropTypes from "prop-types";
import { Link, useForm } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import ListOfIdeas from "@/Components/GiftList/Auth/Ideas/ListOfIdeas";
import EditListTitle from "@/Components/GiftList/Auth/Lists/EditListTitle";
import SmallButton from "@/Components/Buttons/SmallButton";
import { toast } from "sonner";

export default function AuthList({ auth, list, ideas }) {
    // console.log("ideas : ", ideas);

    const [editing, setEditing] = useState(false);

    const { patch, reset } = useForm();

    const archiveList = (e) => {
        e.preventDefault();
        patch(route("lists.archive", list), {
            onSuccess: () => reset(),
            onError: (errors) => {
                console.error(
                    "Erreur lors de l'archivage de la liste :",
                    errors
                );
            },
        });
        toast.success("Liste archivée !");
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="sm:flex items-center justify-between">
                    <div className="flex items-center">
                        {editing ? (
                            <EditListTitle
                                list={list}
                                setEditing={setEditing}
                            />
                        ) : (
                            <>
                                <h2 className="font-semibold text-xl text-gray-800 leading-tight mr-3">
                                    Ma liste "{list.name}"
                                </h2>
                                <button onClick={() => setEditing(true)}>
                                    <svg
                                        xmlns="https://www.w3.org/2000/svg"
                                        className="h-5 w-5 text-gray-400 hover:text-orange-500"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                </button>
                            </>
                        )}
                    </div>
                    {ideas.length > 0 && (
                        <div className="flex flex-wrap mt-2 sm:mt-0">
                            <button
                                onClick={archiveList}
                                className="flex items-center mr-5"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    className="w-5 h-5  mr-1"
                                >
                                    <path d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                </svg>
                                <p className=" hover:text-orange-500 text-sm">
                                    Archiver la liste
                                </p>
                            </button>
                            <Link
                                as="button"
                                href={route("ideas.create_idea", list.id)}
                                className="flex items-center"
                            >
                                <svg
                                    xmlns="https://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    className="w-5 h-5 mr-1"
                                >
                                    <path d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p className=" hover:text-orange-500 text-sm">
                                    Compléter la liste
                                </p>
                            </Link>
                        </div>
                    )}
                </div>
            }
        >
            <div className="max-w-4xl mx-auto pb-14 px-4 mt-6">
                {ideas.length > 0 ? (
                    <div className="flex w-full" key={list.id}>
                        <ListOfIdeas list={list} ideas={ideas} auth={auth} />
                    </div>
                ) : (
                    <div className="text-center">
                        <p>
                            Votre liste est vide. Cliquez sur ce bouton pour la
                            compléter :
                        </p>
                        <Link
                            as="button"
                            href={route("ideas.create_idea", list.id)}
                            className="mt-2"
                        >
                            <SmallButton>Commencer la liste</SmallButton>
                        </Link>
                    </div>
                )}
            </div>
        </AuthenticatedLayout>
    );
}

AuthList.propTypes = {
    auth: PropTypes.object.isRequired,
    list: PropTypes.array,
    ideas: PropTypes.array,
};
