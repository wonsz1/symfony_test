import { FieldGuesser, ShowGuesser } from "@api-platform/admin";
import { ReferenceField, TextField, DateField } from "react-admin";


export const PostShow = () => (
    <ShowGuesser>
        <FieldGuesser source="title" />
        <FieldGuesser source="content" />
        <FieldGuesser source="excerpt" />
        <FieldGuesser source="status" />
        <FieldGuesser source="viewCount" />
        <DateField source="createdAt" />
        <DateField source="publishedAt" />
        <ReferenceField source="author.@id" reference="users" link="show" label="Author">
            <TextField source="email" />
        </ReferenceField>
        <ReferenceField source="category.@id" reference="categories" link="show" label="Category">
            <TextField source="name" />
        </ReferenceField>
    </ShowGuesser>
);