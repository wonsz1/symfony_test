import { HydraAdmin, ResourceGuesser } from '@api-platform/admin';

export const App = () => (
  <HydraAdmin entrypoint="http://localhost:8080/api">
    <ResourceGuesser name="posts" />
    <ResourceGuesser name="categories" />
    <ResourceGuesser name="comments" />
    <ResourceGuesser name="users" />
  </HydraAdmin>
);
