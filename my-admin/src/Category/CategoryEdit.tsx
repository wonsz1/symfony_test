import { InputGuesser, EditGuesser } from "@api-platform/admin";

export const CategoryEdit = () => (
    <EditGuesser mutationMode="undoable" warnWhenUnsavedChanges>
        <InputGuesser source="name" />
        <InputGuesser source="description" />
    </EditGuesser>
);
