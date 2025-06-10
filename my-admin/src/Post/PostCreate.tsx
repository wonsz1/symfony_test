import { CreateGuesser, InputGuesser } from "@api-platform/admin";
import { ReferenceInput, AutocompleteInput } from 'react-admin';

export const PostCreate = () => (
    <CreateGuesser>
        <InputGuesser source="title" />
        <InputGuesser source="content" multiline />
        <InputGuesser source="slug" />
        <InputGuesser source="status" />
        <ReferenceInput source="author" label="Author" reference="users">
            <AutocompleteInput label="Author" filterToQuery={(searchText: string) => ({ email: searchText })} />
        </ReferenceInput>
        <ReferenceInput source="category" label="Category" reference="categories">
            <AutocompleteInput label="Category" filterToQuery={(searchText: string) => ({ name: searchText })} />
        </ReferenceInput>
    </CreateGuesser>
);