CREATE PROCEDURE fillMonData(
    PokemonId1 INT,
    PokedexId1 INT,
    Level1 INT
)
BEGIN

    UPDATE pokemon p JOIN
    pokedex base ON base.PokedexId=p.PokedexId
    JOIN pokeiv iv ON iv.PokemonId=p.PokemonId
    SET p.HP=floor(0.01 * ((2 * base.BaseHP + iv.IVHP)* Level1))+ Level1 + 10, 
    p.HPLeft=floor(0.01 * ((2 * base.BaseHP + iv.IVHP)* Level1))+ Level1 + 10,
    p.Attack=floor(0.01 * ((2 * base.BaseAttack + iv.IVAttack)* Level1) + 5), 
    p.Defense=floor(0.01 * ((2 * base.BaseDefense + iv.IVDefense)* Level1) + 5), 
    p.SpAtk=floor(0.01 * ((2 * base.BaseSpAtk + iv.IVSpAtk)* Level1) + 5), 
    p.SpDef=floor(0.01 * ((2 * base.BaseSpDef + iv.IVSpDef)* Level1) + 5), 
    p.Speed=floor(0.01 * ((2 * base.BaseSpeed + iv.IVSpeed)* Level1) + 5) 
    where p.PokemonId=PokemonId1;

    INSERT INTO pokeiv (`pokemonId`, `IVHP`, `IVAttack`, `IVDefense`, `IVSpAtk`, `IVSpDef`, `IVSpeed`) 
    VALUE (
PokemonId1, 
FLOOR(RAND()*(31)+1), 
FLOOR(RAND()*(31)+1),
FLOOR(RAND()*(31)+1),
FLOOR(RAND()*(31)+1),
FLOOR(RAND()*(31)+1),
FLOOR(RAND()*(31)+1)
);

END;